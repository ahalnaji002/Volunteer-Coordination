<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VolunteerSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $user = User::updateOrCreate(
                ['email' => 'volunteer@example.com'],
                [
                    'name' => 'Sample Volunteer',
                    'password' => Hash::make('password123'),
                    'role' => 'volunteer',
                ]
            );

            Volunteer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => '0590000000',
                    'national_id' => '900000001',
                ]
            );
        });
    }
}
