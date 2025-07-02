<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Database\Factories\EventFactory::new()
            ->count(10)
            ->create();
    }
}
