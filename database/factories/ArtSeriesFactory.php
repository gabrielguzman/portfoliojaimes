<?php

namespace Database\Factories;

use App\Models\ArtSeries;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtSeriesFactory extends Factory
{
    protected $model = ArtSeries::class;

    public function definition(): array
    {
        return ['title' => fake()->sentence(3), 'slug' => fake()->unique()->slug(), 'description' => fake()->paragraph(), 'published' => false];
    }
}
