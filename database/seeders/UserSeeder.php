<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if a user with the specified email already exists
        $existingUser = User::where('email', config('constant.ADMIN_EMAIL'))->first();

        // If no user exists with this email, proceed to insert a new user
        if (!$existingUser) {
            User::create([
                'name'              => 'Divyesh',
                'email'             => config('constant.ADMIN_EMAIL'),
                'email_verified_at' => now(),
                'password'          => Hash::make(config('constant.ADMIN_PASSWORD')),
            ]);
        }
    }
}
