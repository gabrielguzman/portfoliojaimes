<?php

namespace Database\Factories;

use App\Models\PortfolioEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class PortfolioEventFactory extends Factory
{
    protected $model = PortfolioEvent::class;

    public function definition(): array
    {
        return ['title' => fake()->sentence(3), 'slug' => fake()->unique()->slug(), 'description' => fake()->paragraph(), 'published' => false, 'kind' => 'exhibition', 'starts_on' => '2026-12-01', 'location' => 'Espacio de arte'];
    }
}
