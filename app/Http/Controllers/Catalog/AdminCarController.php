<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminCarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cars = Car::with('images')->latest()->get();

        return Inertia::render('Catalog/AdminCarsPage', [
            'cars' => $cars
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Catalog/CarCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'number_plate' => ['nullable', 'string', 'max:20', Rule::unique('cars', 'number_plate')],
                'brand' => 'required',
                'model' => 'required',
                'year'  => 'required|integer|min:1900|max:' . date('Y'),
                'car_type'  => 'required',
                'daily_rent_price' => 'required|numeric|min:0',
                'hourly_rent_price' => 'required|numeric|min:0',
                'fuel_consumption_rate' => 'nullable|numeric|min:0',
                'status' => ['required', Rule::in([
                    Car::STATUS_AVAILABLE,
                    Car::STATUS_UNDER_MAINTENANCE,
                ])],
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ]
        );

        $data = [
            'name' => $request->input('name'),
            'number_plate' => $this->normalizeNumberPlate($request->input('number_plate')),
            'brand' => $request->input('brand'),
            'model' => $request->input('model'),
            'year' => $request->input('year'),
            'car_type' => $request->input('car_type'),
            'daily_rent_price' => $request->input('daily_rent_price'),
            'hourly_rent_price' => $request->input('hourly_rent_price'),
            'fuel_consumption_rate' => $request->input('fuel_consumption_rate'),
            'status' => $request->input('status'),
        ];

        DB::transaction(function () use ($request, $data): void {
            $car = Car::create($data);

            $this->storeImages($car, $request->file('images', []));
            $this->normalizeImages($car);
        });

        return redirect()->route('cars.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $car = Car::with('images')->findOrFail($id);

        return Inertia::render('Catalog/CarEdit', [
            'car' => $car
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'name' => 'required',
                'number_plate' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('cars', 'number_plate')->ignore($id),
                ],
                'brand' => 'required',
                'model' => 'required',
                'year'  => 'required|integer|min:1900|max:' . date('Y'),
                'car_type'  => 'required',
                'daily_rent_price' => 'required|numeric|min:0',
                'hourly_rent_price' => 'required|numeric|min:0',
                'fuel_consumption_rate' => 'nullable|numeric|min:0',
                'status' => ['required', Rule::in([
                    Car::STATUS_AVAILABLE,
                    Car::STATUS_UNDER_MAINTENANCE,
                ])],
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'removed_image_ids' => 'nullable|array',
                'removed_image_ids.*' => 'integer',
            ]
        );

        DB::transaction(function () use ($request, $id): void {
            $car = Car::with('images')->findOrFail($id);

            $car->update($request->only([
                'name',
                'brand',
                'model',
                'year',
                'car_type',
                'daily_rent_price',
                'hourly_rent_price',
                'fuel_consumption_rate',
                'status',
            ]) + [
                'number_plate' => $this->normalizeNumberPlate($request->input('number_plate')),
            ]);

            $car->images()
                ->whereIn('id', $request->input('removed_image_ids', []))
                ->get()
                ->each(function ($image): void {
                    $this->deleteStoredImage($image->path);
                    $image->delete();
                });

            $this->storeImages($car, $request->file('images', []));
            $this->normalizeImages($car);
        });

        return redirect()->route('cars.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $car = Car::findOrFail($id);

        if ($car->rentals()->exists()) {
            return redirect()->route('cars.index')->with([
                'error' => __('admin.flash.car_delete_blocked'),
            ]);
        }

        $car->images->each(fn ($image) => $this->deleteStoredImage($image->path));

        $car->delete();

        return redirect()->route('cars.index')->with([
            'success' => __('admin.flash.car_deleted'),
        ]);
    }

    private function storeImages(Car $car, array $images): void
    {
        $position = (int) $car->images()->max('position') + 1;

        foreach ($images as $image) {
            $car->images()->create([
                'path' => $image->store('uploads', 'public'),
                'position' => $position++,
            ]);
        }
    }

    private function normalizeNumberPlate(?string $numberPlate): ?string
    {
        $numberPlate = trim((string) $numberPlate);

        return $numberPlate === '' ? null : strtoupper($numberPlate);
    }

    private function normalizeImages(Car $car): void
    {
        if (! $car->images()->exists()) {
            $car->images()->create([
                'path' => 'placeholder.png',
                'position' => 0,
            ]);
        }

        $images = $car->images()
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        foreach ($images as $position => $image) {
            $image->update([
                'position' => $position,
                'is_primary' => $position === 0,
            ]);
        }
    }

    private function deleteStoredImage(string $path): void
    {
        if ($path !== 'placeholder.png' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
