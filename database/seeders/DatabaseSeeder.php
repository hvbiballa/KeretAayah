<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUser(
            name: 'Admin2',
            email: 'admin2@keretaayah.myuni.agency',
            role: 'admin',
            passwordEnv: 'SEEDED_ADMIN_PASSWORD',
        );

        $this->seedUser(
            name: 'Staff2',
            email: 'staff2@keretaayah.myuni.agency',
            role: 'staff',
            passwordEnv: 'SEEDED_STAFF_PASSWORD',
        );
    }

    private function seedUser(string $name, string $email, string $role, string $passwordEnv): void
    {
        $user = User::where('email', $email)->first();
        $created = false;
        $password = null;

        if (! $user) {
            $created = true;
            $password = env($passwordEnv) ?: $this->generatePassword();

            $user = new User([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
        }

        $user->role = $role;
        $user->email_verified_at = $user->email_verified_at ?: now();
        $user->save();

        if ($created) {
            $source = env($passwordEnv) ? "from {$passwordEnv}" : 'generated';

            $this->command?->line('');
            $this->command?->warn("Created {$role} account: {$email}");
            $this->command?->warn("Initial password ({$source}): {$password}");
            $this->command?->warn('Save this password securely and change it after first login.');
        } else {
            $this->command?->info("Updated existing {$role} account metadata: {$email}");
        }
    }

    private function generatePassword(): string
    {
        return Str::password(length: 20, letters: true, numbers: true, symbols: true, spaces: false);
    }
}
