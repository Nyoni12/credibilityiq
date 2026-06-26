<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    protected $signature   = 'credibilityiq:create-superadmin';
    protected $description = 'Create the SuperAdmin user from environment variables';

    public function handle(): int
    {
        $email = env('SUPERADMIN_EMAIL', 'admin@credibilityiq.com');

        if (User::where('email', $email)->exists()) {
            $this->info("SuperAdmin already exists: {$email}");
            return self::SUCCESS;
        }

        User::create([
            'first_name' => env('SUPERADMIN_FIRST_NAME', 'Super'),
            'last_name'  => env('SUPERADMIN_LAST_NAME',  'Admin'),
            'email'      => $email,
            'password'   => Hash::make(env('SUPERADMIN_PASSWORD', 'ChangeMe123!')),
            'role'       => 'superadmin',
            'company_id' => null,
            'is_active'  => true,
        ]);

        $this->info("SuperAdmin created: {$email}");
        return self::SUCCESS;
    }
}
