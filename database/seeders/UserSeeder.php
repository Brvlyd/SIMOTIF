<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate the users table first to avoid duplicate entries
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = [
            [
                'name' => 'Owner',
                'email' => 'owner@simotif.com',
                'password' => Hash::make('password'),
                'role' => 'owner'
            ],
            [
                'name' => 'Warehouse Staff',
                'email' => 'warehouse@simotif.com',
                'password' => Hash::make('password'),
                'role' => 'warehouse'
            ],
            [
                'name' => 'Sales Staff',
                'email' => 'sales@simotif.com',
                'password' => Hash::make('password'),
                'role' => 'sales'
            ]
        ];

        foreach ($users as $userData) {
            try {
                User::create($userData);
            } catch (\Exception $e) {
                $this->command->error("Error creating user {$userData['email']}: " . $e->getMessage());
            }
        }

        $this->command->info('Users seeded successfully!');
    }
}