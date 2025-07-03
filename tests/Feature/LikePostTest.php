<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikePostTest extends TestCase
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
        $this->post = Post::factory()->create(['thread_id' => $this->thread->id, 'user_id' => $this->user->id]);
    }

    public function test_authenticated_users_can_like_posts(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->post("/posts/{$this->post->id}/like");

        $response->assertStatus(200);
        $response->assertJson(['liked' => true, 'likes_count' => 1]);
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->otherUser->id,
            'post_id' => $this->post->id,
        ]);
    }

    public function test_authenticated_users_can_unlike_posts(): void
    {
        // First like the post
        $this->actingAs($this->otherUser)
            ->post("/posts/{$this->post->id}/like");

        // Then unlike it
        $response = $this->actingAs($this->otherUser)
            ->post("/posts/{$this->post->id}/like");

        $response->assertStatus(200);
        $response->assertJson(['liked' => false, 'likes_count' => 0]);
        
        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->otherUser->id,
            'post_id' => $this->post->id,
        ]);
    }

    public function test_unauthenticated_users_cannot_like_posts(): void
    {
        $response = $this->post("/posts/{$this->post->id}/like");

        $response->assertRedirect('/login');
        
        $this->assertDatabaseMissing('likes', [
            'post_id' => $this->post->id,
        ]);
    }

    public function test_users_cannot_like_nonexistent_posts(): void
    {
        $response = $this->actingAs($this->user)
            ->post("/posts/999/like");

        $response->assertStatus(404);
    }

    public function test_multiple_users_can_like_same_post(): void
    {
        $thirdUser = User::factory()->create();

        // First user likes
        $this->actingAs($this->otherUser)
            ->post("/posts/{$this->post->id}/like");

        // Second user likes
        $response = $this->actingAs($thirdUser)
            ->post("/posts/{$this->post->id}/like");

        $response->assertStatus(200);
        $response->assertJson(['liked' => true, 'likes_count' => 2]);
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->otherUser->id,
            'post_id' => $this->post->id,
        ]);
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $thirdUser->id,
            'post_id' => $this->post->id,
        ]);
    }

    public function test_post_shows_correct_like_count_and_user_like_status(): void
    {
        // Create some likes
        $thirdUser = User::factory()->create();
        $this->actingAs($this->otherUser)->post("/posts/{$this->post->id}/like");
        $this->actingAs($thirdUser)->post("/posts/{$this->post->id}/like");

        // Check as authenticated user who hasn't liked
        $response = $this->actingAs($this->user)
            ->get("/forums/{$this->forum->slug}/threads/{$this->thread->slug}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('posts.data.0.likes_count', 2)
            ->where('posts.data.0.is_liked_by_user', false)
        );

        // Check as authenticated user who has liked
        $response = $this->actingAs($this->otherUser)
            ->get("/forums/{$this->forum->slug}/threads/{$this->thread->slug}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('posts.data.0.likes_count', 2)
            ->where('posts.data.0.is_liked_by_user', true)
        );
    }

    public function test_like_counts_are_updated_correctly(): void
    {
        $this->assertEquals(0, $this->post->fresh()->likes_count);

        // Like the post
        $this->actingAs($this->otherUser)
            ->post("/posts/{$this->post->id}/like");

        $this->assertEquals(1, $this->post->fresh()->likes_count);

        // Unlike the post
        $this->actingAs($this->otherUser)
            ->post("/posts/{$this->post->id}/like");

        $this->assertEquals(0, $this->post->fresh()->likes_count);
    }
}