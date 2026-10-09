<?php

namespace Database\Factories;

use App\Models\TeachingMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeachingMaterialFactory extends Factory
{
    protected $model = TeachingMaterial::class;

    public function definition(): array
    {
        return ['title' => fake()->sentence(3), 'slug' => fake()->unique()->slug(), 'description' => fake()->paragraph(), 'published' => false, 'file' => 'materials/guia.pdf'];
    }
}
