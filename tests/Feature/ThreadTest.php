<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    public function test_thread_show_returns_successful_response(): void
    {
        $user = User::factory()->create();
        
        $category = Category::create([
            'name' => 'General',
            'slug' => 'general',
        ]);

        $forum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Announcements',
            'slug' => 'announcements',
        ]);

        $thread = Thread::create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'title' => 'Test Thread',
            'slug' => 'test-thread',
        ]);

        $response = $this->get("/forums/{$forum->slug}/threads/{$thread->slug}");

        $response->assertStatus(200);
    }

    public function test_thread_show_increments_view_count(): void
    {
        $user = User::factory()->create();
        
        $category = Category::create([
            'name' => 'General',
            'slug' => 'general',
        ]);

        $forum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Announcements',
            'slug' => 'announcements',
        ]);

        $thread = Thread::create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'title' => 'Test Thread',
            'slug' => 'test-thread',
            'views_count' => 5,
        ]);

        $this->get("/forums/{$forum->slug}/threads/{$thread->slug}");

        $thread->refresh();
        $this->assertEquals(6, $thread->views_count);
    }
}
