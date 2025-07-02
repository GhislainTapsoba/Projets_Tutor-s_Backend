<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\EventSeeder;
use Database\Seeders\TicketSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@example.com',
        ]);

        $this->call([
            EventSeeder::class,
            TicketSeeder::class
        ]);
    }
}
