<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'     => fake()->sentence(6, true),
            'content'   => fake()->paragraphs(3, true),
        ];
    }

    /**
     * Indicate that the model should be published.
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'published_at'  => now(),
        ]);
    }
}
