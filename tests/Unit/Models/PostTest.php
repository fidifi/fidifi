<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_has_fillable_attributes(): void
    {
        $post = new Post();
        
        $this->assertEquals([
            'thread_id',
            'user_id',
            'content',
            'is_solution',
            'likes_count',
            'edited_at',
            'edited_by',
        ], $post->getFillable());
    }

    public function test_post_casts_attributes_correctly(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $forum = Forum::create(['category_id' => $category->id, 'name' => 'Test', 'slug' => 'test']);
        $thread = Thread::create(['forum_id' => $forum->id, 'user_id' => $user->id, 'title' => 'Test', 'slug' => 'test']);

        $post = Post::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'Test content',
            'is_solution' => '1',
            'likes_count' => '5',
            'edited_at' => '2023-01-01 12:00:00',
        ]);

        $this->assertIsBool($post->is_solution);
        $this->assertIsInt($post->likes_count);
        $this->assertInstanceOf(\Carbon\Carbon::class, $post->edited_at);
        $this->assertTrue($post->is_solution);
        $this->assertEquals(5, $post->likes_count);
    }

    public function test_post_belongs_to_thread(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $forum = Forum::create(['category_id' => $category->id, 'name' => 'Test', 'slug' => 'test']);
        $thread = Thread::create(['forum_id' => $forum->id, 'user_id' => $user->id, 'title' => 'Test', 'slug' => 'test']);

        $post = Post::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'Test content',
        ]);

        $this->assertInstanceOf(Thread::class, $post->thread);
        $this->assertEquals($thread->id, $post->thread->id);
    }

    public function test_post_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $forum = Forum::create(['category_id' => $category->id, 'name' => 'Test', 'slug' => 'test']);
        $thread = Thread::create(['forum_id' => $forum->id, 'user_id' => $user->id, 'title' => 'Test', 'slug' => 'test']);

        $post = Post::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'Test content',
        ]);

        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($user->id, $post->user->id);
    }

    public function test_post_belongs_to_editor(): void
    {
        $user = User::factory()->create();
        $editor = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $forum = Forum::create(['category_id' => $category->id, 'name' => 'Test', 'slug' => 'test']);
        $thread = Thread::create(['forum_id' => $forum->id, 'user_id' => $user->id, 'title' => 'Test', 'slug' => 'test']);

        $post = Post::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'Test content',
            'edited_by' => $editor->id,
        ]);

        $this->assertInstanceOf(User::class, $post->editor);
        $this->assertEquals($editor->id, $post->editor->id);
    }
}
