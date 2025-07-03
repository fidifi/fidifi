<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Forum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumTest extends TestCase
{
    use RefreshDatabase;

    public function test_forums_index_returns_successful_response(): void
    {
        $category = Category::create([
            'name' => 'General',
            'slug' => 'general',
            'description' => 'General discussions',
        ]);

        $forum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Announcements',
            'slug' => 'announcements',
            'description' => 'Official announcements',
        ]);

        $response = $this->get('/forums');

        $response->assertStatus(200);
    }

    public function test_forum_show_returns_successful_response(): void
    {
        $category = Category::create([
            'name' => 'General',
            'slug' => 'general',
        ]);

        $forum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Announcements',
            'slug' => 'announcements',
        ]);

        $response = $this->get("/forums/{$forum->slug}");

        $response->assertStatus(200);
    }
}
