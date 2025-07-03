<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    public function test_thread_last_post_relationship(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $forum = Forum::create(['category_id' => $category->id, 'name' => 'Test', 'slug' => 'test']);

        $thread = Thread::create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'title' => 'Test Thread',
            'slug' => 'test-thread',
        ]);

        $post = Post::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'Last post',
        ]);

        $thread->update(['last_post_id' => $post->id]);
        $thread->refresh();

        $this->assertInstanceOf(Post::class, $thread->lastPost);
        $this->assertEquals($post->id, $thread->lastPost->id);
    }
}
