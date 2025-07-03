<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateThreadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->forum = Forum::factory()->create(['category_id' => $this->category->id]);
    }

    public function test_create_thread_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)
            ->get("/forums/{$this->forum->slug}/threads/create");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Threads/Create')
            ->has('forum')
            ->where('forum.slug', $this->forum->slug)
        );
    }

    public function test_unauthenticated_users_cannot_access_create_thread_page(): void
    {
        $response = $this->get("/forums/{$this->forum->slug}/threads/create");

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_create_threads(): void
    {
        $response = $this->actingAs($this->user)
            ->post("/forums/{$this->forum->slug}/threads", [
                'title' => 'My New Thread',
                'content' => 'This is the content of my new thread.',
            ]);

        $this->assertDatabaseHas('threads', [
            'title' => 'My New Thread',
            'slug' => 'my-new-thread',
            'forum_id' => $this->forum->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('posts', [
            'content' => 'This is the content of my new thread.',
            'user_id' => $this->user->id,
        ]);

        $thread = $this->forum->threads()->where('title', 'My New Thread')->first();
        $response->assertRedirect(route('threads.show', [
            'forum' => $this->forum->slug,
            'thread' => $thread->slug
        ]));
    }

    public function test_thread_title_is_required(): void
    {
        $response = $this->actingAs($this->user)
            ->post("/forums/{$this->forum->slug}/threads", [
                'title' => '',
                'content' => 'This is the content.',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_thread_content_is_required(): void
    {
        $response = $this->actingAs($this->user)
            ->post("/forums/{$this->forum->slug}/threads", [
                'title' => 'My Thread',
                'content' => '',
            ]);

        $response->assertSessionHasErrors('content');
    }

    public function test_thread_title_must_be_unique_in_forum(): void
    {
        // Create existing thread
        $existingThread = \App\Models\Thread::factory()->create([
            'title' => 'Existing Thread',
            'forum_id' => $this->forum->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/forums/{$this->forum->slug}/threads", [
                'title' => 'Existing Thread',
                'content' => 'Different content.',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_thread_title_can_be_same_in_different_forums(): void
    {
        $otherForum = Forum::factory()->create(['category_id' => $this->category->id]);
        
        // Create thread in first forum
        \App\Models\Thread::factory()->create([
            'title' => 'Same Title',
            'forum_id' => $this->forum->id,
        ]);

        // Should be able to create thread with same title in different forum
        $response = $this->actingAs($this->user)
            ->post("/forums/{$otherForum->slug}/threads", [
                'title' => 'Same Title',
                'content' => 'Content for other forum.',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('threads', [
            'title' => 'Same Title',
            'forum_id' => $otherForum->id,
        ]);
    }

    public function test_creating_thread_increments_forum_counters(): void
    {
        $initialThreadsCount = $this->forum->threads_count;
        $initialPostsCount = $this->forum->posts_count;

        $this->actingAs($this->user)
            ->post("/forums/{$this->forum->slug}/threads", [
                'title' => 'New Thread',
                'content' => 'Thread content.',
            ]);

        $this->forum->refresh();
        $this->assertEquals($initialThreadsCount + 1, $this->forum->threads_count);
        $this->assertEquals($initialPostsCount + 1, $this->forum->posts_count);
    }

    public function test_creating_thread_increments_user_counters(): void
    {
        $initialThreadsCount = $this->user->threads_count;
        $initialPostsCount = $this->user->posts_count;

        $this->actingAs($this->user)
            ->post("/forums/{$this->forum->slug}/threads", [
                'title' => 'New Thread',
                'content' => 'Thread content.',
            ]);

        $this->user->refresh();
        $this->assertEquals($initialThreadsCount + 1, $this->user->threads_count);
        $this->assertEquals($initialPostsCount + 1, $this->user->posts_count);
    }

    public function test_unauthenticated_users_cannot_create_threads(): void
    {
        $response = $this->post("/forums/{$this->forum->slug}/threads", [
            'title' => 'New Thread',
            'content' => 'Thread content.',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('threads', [
            'title' => 'New Thread',
        ]);
    }

    public function test_thread_slug_is_generated_correctly(): void
    {
        $this->actingAs($this->user)
            ->post("/forums/{$this->forum->slug}/threads", [
                'title' => 'This is a Thread with Special Characters!',
                'content' => 'Thread content.',
            ]);

        $this->assertDatabaseHas('threads', [
            'title' => 'This is a Thread with Special Characters!',
            'slug' => 'this-is-a-thread-with-special-characters',
        ]);
    }
}