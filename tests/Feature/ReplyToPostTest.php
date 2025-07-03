<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyToPostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->forum = Forum::factory()->create(['category_id' => $this->category->id]);
        $this->thread = Thread::factory()->create(['forum_id' => $this->forum->id, 'user_id' => $this->user->id]);
        $this->originalPost = Post::factory()->create(['thread_id' => $this->thread->id, 'user_id' => $this->user->id]);
    }

    public function test_authenticated_users_can_reply_to_posts(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->post("/posts/{$this->originalPost->id}/reply", [
                'content' => 'This is my reply to your post.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Reply posted successfully');
        
        $this->assertDatabaseHas('posts', [
            'thread_id' => $this->thread->id,
            'user_id' => $this->otherUser->id,
            'content' => 'This is my reply to your post.',
            'parent_post_id' => $this->originalPost->id,
        ]);
    }

    public function test_authenticated_users_can_quote_posts_in_replies(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->post("/posts/{$this->originalPost->id}/reply", [
                'content' => 'I agree with what you said!',
                'quoted_content' => $this->originalPost->content,
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('posts', [
            'thread_id' => $this->thread->id,
            'user_id' => $this->otherUser->id,
            'content' => 'I agree with what you said!',
            'parent_post_id' => $this->originalPost->id,
            'quoted_content' => $this->originalPost->content,
        ]);
    }

    public function test_unauthenticated_users_cannot_reply_to_posts(): void
    {
        $response = $this->post("/posts/{$this->originalPost->id}/reply", [
            'content' => 'This should not work.',
        ]);

        $response->assertRedirect('/login');
        
        $this->assertDatabaseMissing('posts', [
            'content' => 'This should not work.',
        ]);
    }

    public function test_reply_content_is_required(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->postJson("/posts/{$this->originalPost->id}/reply", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }

    public function test_reply_content_must_have_minimum_length(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->postJson("/posts/{$this->originalPost->id}/reply", [
                'content' => 'hi',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }

    public function test_users_cannot_reply_to_nonexistent_posts(): void
    {
        $response = $this->actingAs($this->user)
            ->post("/posts/999/reply", [
                'content' => 'This should fail.',
            ]);

        $response->assertStatus(404);
    }

    public function test_replies_increment_thread_and_forum_counters(): void
    {
        $initialThreadPosts = $this->thread->posts_count;
        $initialForumPosts = $this->forum->posts_count;
        $initialUserPosts = $this->otherUser->posts_count;

        $this->actingAs($this->otherUser)
            ->post("/posts/{$this->originalPost->id}/reply", [
                'content' => 'This is my reply.',
            ]);

        $this->assertEquals($initialThreadPosts + 1, $this->thread->fresh()->posts_count);
        $this->assertEquals($initialForumPosts + 1, $this->forum->fresh()->posts_count);
        $this->assertEquals($initialUserPosts + 1, $this->otherUser->fresh()->posts_count);
    }

    public function test_thread_shows_reply_hierarchy(): void
    {
        // Create a reply
        $this->actingAs($this->otherUser)
            ->post("/posts/{$this->originalPost->id}/reply", [
                'content' => 'This is my reply.',
            ]);

        // View the thread
        $response = $this->actingAs($this->user)
            ->get("/forums/{$this->forum->slug}/threads/{$this->thread->slug}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('posts.data', 2) // Original post + reply
            ->where('posts.data.1.parent_post_id', $this->originalPost->id)
            ->where('posts.data.1.content', 'This is my reply.')
        );
    }

    public function test_nested_replies_work_correctly(): void
    {
        // Create first reply
        $firstReply = $this->actingAs($this->otherUser)
            ->post("/posts/{$this->originalPost->id}/reply", [
                'content' => 'First reply.',
            ]);

        $firstReplyId = Post::where('content', 'First reply.')->first()->id;

        // Reply to the reply
        $this->actingAs($this->user)
            ->post("/posts/{$firstReplyId}/reply", [
                'content' => 'Reply to the reply.',
            ]);

        $this->assertDatabaseHas('posts', [
            'content' => 'Reply to the reply.',
            'parent_post_id' => $firstReplyId,
        ]);
    }

    public function test_quoted_content_is_properly_formatted(): void
    {
        $this->actingAs($this->otherUser)
            ->post("/posts/{$this->originalPost->id}/reply", [
                'content' => 'I agree!',
                'quoted_content' => $this->originalPost->content,
            ]);

        $reply = Post::where('content', 'I agree!')->first();
        
        $this->assertEquals($this->originalPost->content, $reply->quoted_content);
        $this->assertEquals($this->originalPost->id, $reply->parent_post_id);
    }
}