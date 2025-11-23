<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::firstOrCreate(
            ["username" => "admin"],
            [
                "email" => "admin123@example.com",
                "password" => Hash::make("12345"),
                "role" => "admin",
            ],
        );
    }
}
