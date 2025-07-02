<?php

namespace Database\Factories\Models;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'start_date' => $this->faker->dateTimeBetween('+1 week', '+2 months'),
            'end_date' => $this->faker->dateTimeBetween('+2 months', '+3 months'),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'capacity' => $this->faker->numberBetween(50, 500),
            'location' => $this->faker->address,
            'image_url' => $this->faker->imageUrl(640, 480, 'concerts')
        ];
    }
}
