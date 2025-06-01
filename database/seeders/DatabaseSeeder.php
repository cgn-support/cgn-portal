<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'manager',
        //     'email' => 'manager@example.com',
        //     'password' => bcrypt('asdf6900'),
        // ]);

        $this->call([
            RoleSeeder::class, // Add this line
            // ... other seeders
        ]);
    }
}
