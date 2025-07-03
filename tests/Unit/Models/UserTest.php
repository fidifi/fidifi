<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_threads(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $forum = Forum::create(['category_id' => $category->id, 'name' => 'Test', 'slug' => 'test']);

        $thread1 = Thread::create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'title' => 'Thread 1',
            'slug' => 'thread-1',
        ]);

        $thread2 = Thread::create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'title' => 'Thread 2',
            'slug' => 'thread-2',
        ]);

        $this->assertEquals(2, $user->threads->count());
        $this->assertTrue($user->threads->contains($thread1));
        $this->assertTrue($user->threads->contains($thread2));
    }

    public function test_user_has_many_posts(): void
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

        $post1 = Post::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'First post',
        ]);

        $post2 = Post::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'Second post',
        ]);

        $this->assertEquals(2, $user->posts->count());
        $this->assertTrue($user->posts->contains($post1));
        $this->assertTrue($user->posts->contains($post2));
    }
}
