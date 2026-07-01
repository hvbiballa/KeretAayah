<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->decimal('hourly_rent_price', 10, 2)->default(0)->after('daily_rent_price');
            $table->string('status')->default('Available')->after('hourly_rent_price');
        });

        Schema::create('car_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        DB::table('cars')
            ->select(['id', 'image', 'availablity'])
            ->orderBy('id')
            ->each(function (object $car): void {
                DB::table('cars')
                    ->where('id', $car->id)
                    ->update([
                        'status' => $car->availablity ? 'Available' : 'Under Maintenance',
                    ]);

                DB::table('car_images')->insert([
                    'car_id' => $car->id,
                    'path' => $car->image ?: 'placeholder.png',
                    'is_primary' => true,
                    'position' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('availablity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->boolean('availablity')->default(true)->after('daily_rent_price');
        });

        DB::table('cars')
            ->select(['id', 'status'])
            ->orderBy('id')
            ->each(function (object $car): void {
                DB::table('cars')
                    ->where('id', $car->id)
                    ->update([
                        'availablity' => $car->status === 'Available',
                    ]);
            });

        Schema::dropIfExists('car_images');

        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['hourly_rent_price', 'status']);
        });
    }
};
