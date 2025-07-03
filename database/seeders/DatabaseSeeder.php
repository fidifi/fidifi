<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
        ]);

        // Create additional users
        User::factory(10)->create();

        // Seed forums
        $this->call(ForumSeeder::class);
        
        // Seed threads and posts
        $this->call(ThreadSeeder::class);
    }
}
