<?php

namespace Database\Factories;

use App\Models\Thread;
use App\Models\User;
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
            'thread_id' => Thread::factory(),
            'user_id' => User::factory(),
            'parent_post_id' => null,
            'content' => fake()->paragraphs(3, true),
            'quoted_content' => null,
            'is_solution' => false,
            'likes_count' => 0,
            'edited_at' => null,
            'editor_id' => null,
        ];
    }
    
    /**
     * Indicate that the post is marked as solution.
     */
    public function solution(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_solution' => true,
        ]);
    }
    
    /**
     * Indicate that the post has been edited.
     */
    public function edited(): static
    {
        return $this->state(fn (array $attributes) => [
            'edited_at' => now(),
            'editor_id' => User::factory(),
        ]);
    }
}