<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status')->default('Pending');
            $table->string('government_id_path')->nullable();
            $table->string('driving_license_path')->nullable();
            $table->date('id_expiry_date')->nullable();
            $table->date('driving_license_expiry_date')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('users')
            ->where('role', 'customer')
            ->pluck('id')
            ->each(function (int $userId) use ($now): void {
                DB::table('customer_verifications')->insert([
                    'user_id' => $userId,
                    'status' => 'Pending',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_verifications');
    }
};
