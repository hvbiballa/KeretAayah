<?php

namespace App\Services\Catalog;

use App\Models\Car;
use App\Models\Rental;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RentalAvailabilityService
{
    private const COOLDOWN_MINUTES = 30;

    private const MINIMUM_HOURLY_SLOT_MINUTES = 120;

    public function hasOverlap(Car $car, Carbon $reservationStart, Carbon $reservationEnd): bool
    {
        $effectiveReservationEnd = $reservationEnd->copy()->addMinutes(self::COOLDOWN_MINUTES);

        return Rental::where('car_id', $car->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('pickup_at', '<=', $effectiveReservationEnd->toDateString())
            ->whereDate('return_at', '>=', $reservationStart->copy()->subDay()->toDateString())
            ->get()
            ->contains(function (Rental $rental) use ($reservationStart, $effectiveReservationEnd): bool {
                [$existingStart, $existingEnd] = $this->effectiveReservationWindow($rental);

                return $existingStart->lt($effectiveReservationEnd)
                    && $existingEnd->gt($reservationStart);
            });
    }

    public function month(Car $car, Carbon $month): array
    {
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        $today = today();

        $rentals = $car->rentals()
            ->where('status', '!=', 'cancelled')
            ->whereDate('pickup_at', '<=', $monthEnd->toDateString())
            ->whereDate('return_at', '>=', $monthStart->copy()->subDay()->toDateString())
            ->get();

        $days = collect(CarbonPeriod::create($monthStart, $monthEnd))
            ->map(function (Carbon $date) use ($car, $rentals, $today): array {
                $dateStart = $date->copy()->startOfDay();
                $dateEnd = $dateStart->copy()->addDay();
                $blockedRanges = [];
                $hasDailyRental = false;

                foreach ($rentals as $rental) {
                    [$reservationStart, $reservationEnd] = $this->reservationWindow($rental);

                    if ($rental->rental_method === 'daily'
                        && $reservationStart->lt($dateEnd)
                        && $reservationEnd->gt($dateStart)) {
                        $hasDailyRental = true;
                    }

                    [$effectiveStart, $effectiveEnd] = $this->effectiveReservationWindow($rental);

                    if (! $effectiveStart->lt($dateEnd) || ! $effectiveEnd->gt($dateStart)) {
                        continue;
                    }

                    $rangeStart = $effectiveStart->greaterThan($dateStart)
                        ? $effectiveStart
                        : $dateStart;
                    $rangeEnd = $effectiveEnd->lessThan($dateEnd)
                        ? $effectiveEnd
                        : $dateEnd;

                    $blockedRanges[] = [
                        'start' => $rangeStart,
                        'end' => $rangeEnd,
                    ];
                }

                $blockedRanges = $this->mergeRanges($blockedRanges);
                $availableRanges = $car->status === Car::STATUS_UNDER_MAINTENANCE
                    ? []
                    : $this->availableRanges($dateStart, $dateEnd, $blockedRanges);

                return [
                    'date' => $date->toDateString(),
                    'status' => $this->dayStatus(
                        $car,
                        $date,
                        $today,
                        $hasDailyRental,
                        $blockedRanges,
                        $availableRanges,
                    ),
                    'available_ranges' => $availableRanges,
                ];
            })
            ->values()
            ->all();

        return [
            'month' => $monthStart->format('Y-m'),
            'car_status' => $car->status,
            'days' => $days,
        ];
    }

    public function fleetMonth(Carbon $month, string $rentalMethod = 'hourly'): array
    {
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        $cars = Car::orderBy('name')->get();
        $carMonths = $cars->mapWithKeys(fn (Car $car): array => [
            $car->id => $this->month($car, $month),
        ]);

        $days = collect(CarbonPeriod::create($monthStart, $monthEnd))
            ->map(function (Carbon $date) use ($cars, $carMonths, $rentalMethod): array {
                $rangeCounts = [];
                $availableCarsCount = 0;
                $maintenanceCarsCount = 0;

                foreach ($cars as $car) {
                    $day = collect($carMonths[$car->id]['days'])
                        ->firstWhere('date', $date->toDateString());

                    if (! $day) {
                        continue;
                    }

                    if ($day['status'] === 'maintenance') {
                        $maintenanceCarsCount++;
                        continue;
                    }

                    if ($date->isBefore(today())) {
                        continue;
                    }

                    $carIsAvailable = $rentalMethod === 'daily'
                        ? $day['status'] === 'available'
                        : in_array($day['status'], ['available', 'partially_booked'], true);

                    if (! $carIsAvailable) {
                        continue;
                    }

                    $availableCarsCount++;

                    if ($rentalMethod === 'hourly') {
                        foreach ($day['available_ranges'] as $range) {
                            $rangeCounts[$range['label']] = ($rangeCounts[$range['label']] ?? 0) + 1;
                        }
                    }
                }

                if ($date->isBefore(today())) {
                    $status = 'past';
                } elseif ($availableCarsCount === 0 && $maintenanceCarsCount === $cars->count()) {
                    $status = 'maintenance';
                } elseif ($availableCarsCount === 0) {
                    $status = 'booked';
                } elseif ($availableCarsCount === $cars->count()) {
                    $status = 'available';
                } else {
                    $status = 'partially_booked';
                }

                $availableRanges = collect($rangeCounts)
                    ->map(fn (int $count, string $label): array => [
                        'label' => $label,
                        'count' => $count,
                    ])
                    ->sortBy('label')
                    ->values()
                    ->all();

                return [
                    'date' => $date->toDateString(),
                    'status' => $status,
                    'available_ranges' => $availableRanges,
                    'available_cars_count' => $availableCarsCount,
                    'total_cars_count' => $cars->count(),
                ];
            })
            ->values()
            ->all();

        return [
            'month' => $monthStart->format('Y-m'),
            'days' => $days,
            'fleet_size' => $cars->count(),
            'rental_method' => $rentalMethod,
        ];
    }

    public function reservationWindow(Rental $rental): array
    {
        $pickupAt = Carbon::parse($rental->pickup_at);
        $returnAt = Carbon::parse($rental->return_at);

        if ($rental->rental_method === 'daily') {
            return [
                $pickupAt->startOfDay(),
                $returnAt->addDay()->startOfDay(),
            ];
        }

        return [$pickupAt, $returnAt];
    }

    private function effectiveReservationWindow(Rental $rental): array
    {
        [$reservationStart, $reservationEnd] = $this->reservationWindow($rental);

        return [
            $reservationStart,
            $reservationEnd->copy()->addMinutes(self::COOLDOWN_MINUTES),
        ];
    }

    private function mergeRanges(array $ranges): array
    {
        usort($ranges, fn (array $left, array $right): int => $left['start']->getTimestamp() <=> $right['start']->getTimestamp());

        return collect($ranges)->reduce(function (array $merged, array $range): array {
            if ($merged === []) {
                return [$range];
            }

            $lastIndex = array_key_last($merged);

            if ($range['start']->lte($merged[$lastIndex]['end'])) {
                if ($range['end']->gt($merged[$lastIndex]['end'])) {
                    $merged[$lastIndex]['end'] = $range['end'];
                }

                return $merged;
            }

            $merged[] = $range;

            return $merged;
        }, []);
    }

    private function availableRanges(Carbon $dateStart, Carbon $dateEnd, array $blockedRanges): array
    {
        $availableRanges = [];
        $cursor = $dateStart->copy();

        foreach ($blockedRanges as $range) {
            if ($range['start']->gt($cursor)) {
                $this->appendAvailableRange($availableRanges, $cursor, $range['start'], $dateEnd);
            }

            if ($range['end']->gt($cursor)) {
                $cursor = $range['end']->copy();
            }
        }

        if ($cursor->lt($dateEnd)) {
            $this->appendAvailableRange($availableRanges, $cursor, $dateEnd, $dateEnd);
        }

        return $availableRanges;
    }

    private function appendAvailableRange(array &$ranges, Carbon $start, Carbon $end, Carbon $dateEnd): void
    {
        if ($start->diffInMinutes($end) < self::MINIMUM_HOURLY_SLOT_MINUTES) {
            return;
        }

        $displayEnd = $end->equalTo($dateEnd)
            ? $end->copy()->subMinute()
            : $end;

        $ranges[] = [
            'label' => $start->format('g:i A').' - '.$displayEnd->format('g:i A'),
        ];
    }

    private function dayStatus(
        Car $car,
        Carbon $date,
        Carbon $today,
        bool $hasDailyRental,
        array $blockedRanges,
        array $availableRanges,
    ): string {
        if ($car->status === Car::STATUS_UNDER_MAINTENANCE) {
            return 'maintenance';
        }

        if ($date->isBefore($today)) {
            return 'past';
        }

        if ($hasDailyRental) {
            return 'booked';
        }

        if ($blockedRanges !== [] && $availableRanges !== []) {
            return 'partially_booked';
        }

        if ($blockedRanges !== []) {
            return 'booked';
        }

        return 'available';
    }
}
