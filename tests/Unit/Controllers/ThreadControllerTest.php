<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\ThreadController;
use App\Models\Category;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_method_returns_correct_view(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $forum = Forum::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get("/forums/{$forum->slug}/threads/create");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Threads/Create')
            ->has('forum')
            ->where('forum.slug', $forum->slug)
        );
    }
}