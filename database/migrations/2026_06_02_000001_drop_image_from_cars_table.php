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
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('image')->default('placeholder.png')->after('status');
        });

        DB::table('cars')
            ->orderBy('id')
            ->each(function (object $car): void {
                $image = DB::table('car_images')
                    ->where('car_id', $car->id)
                    ->orderByDesc('is_primary')
                    ->orderBy('position')
                    ->value('path');

                DB::table('cars')
                    ->where('id', $car->id)
                    ->update([
                        'image' => $image ?: 'placeholder.png',
                    ]);
            });
    }
};
