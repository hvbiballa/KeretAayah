<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->string('rental_method')->default('daily')->after('car_id');
            $table->dateTime('pickup_at')->nullable()->after('rental_method');
            $table->dateTime('return_at')->nullable()->after('pickup_at');
            $table->decimal('applied_rate', 10, 2)->default(0)->after('return_at');
            $table->decimal('duration_units', 8, 2)->default(0)->after('applied_rate');
        });

        DB::table('rentals')
            ->join('cars', 'cars.id', '=', 'rentals.car_id')
            ->select([
                'rentals.id',
                'rentals.start_date',
                'rentals.end_date',
                'rentals.total_cost',
                'cars.daily_rent_price',
            ])
            ->orderBy('rentals.id')
            ->each(function (object $rental): void {
                $days = max(
                    1,
                    Carbon::parse($rental->start_date)->diffInDays($rental->end_date),
                );

                DB::table('rentals')
                    ->where('id', $rental->id)
                    ->update([
                        'pickup_at' => Carbon::parse($rental->start_date)->startOfDay(),
                        'return_at' => Carbon::parse($rental->end_date)->endOfDay(),
                        'applied_rate' => $rental->daily_rent_price,
                        'duration_units' => $days,
                    ]);
            });

        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('car_id');
            $table->date('end_date')->nullable()->after('start_date');
        });

        DB::table('rentals')
            ->select(['id', 'pickup_at', 'return_at'])
            ->orderBy('id')
            ->each(function (object $rental): void {
                DB::table('rentals')
                    ->where('id', $rental->id)
                    ->update([
                        'start_date' => Carbon::parse($rental->pickup_at)->toDateString(),
                        'end_date' => Carbon::parse($rental->return_at)->toDateString(),
                    ]);
            });

        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'rental_method',
                'pickup_at',
                'return_at',
                'applied_rate',
                'duration_units',
            ]);
        });
    }
};
