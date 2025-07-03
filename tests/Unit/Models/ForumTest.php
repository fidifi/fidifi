<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumTest extends TestCase
{
    use RefreshDatabase;

    public function test_forum_has_fillable_attributes(): void
    {
        $forum = new Forum();
        
        $this->assertEquals([
            'category_id',
            'name',
            'slug',
            'description',
            'order',
            'is_private',
            'threads_count',
            'posts_count',
            'last_post_id',
        ], $forum->getFillable());
    }

    public function test_forum_casts_attributes_correctly(): void
    {
        $category = Category::create([
            'name' => 'Test',
            'slug' => 'test',
        ]);

        $forum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Test Forum',
            'slug' => 'test-forum',
            'order' => '5',
            'is_private' => '1',
            'threads_count' => '10',
            'posts_count' => '50',
        ]);

        $this->assertIsInt($forum->order);
        $this->assertIsBool($forum->is_private);
        $this->assertIsInt($forum->threads_count);
        $this->assertIsInt($forum->posts_count);
        $this->assertEquals(5, $forum->order);
        $this->assertTrue($forum->is_private);
        $this->assertEquals(10, $forum->threads_count);
        $this->assertEquals(50, $forum->posts_count);
    }

    public function test_forum_belongs_to_category(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $forum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Test Forum',
            'slug' => 'test-forum',
        ]);

        $this->assertInstanceOf(Category::class, $forum->category);
        $this->assertEquals($category->id, $forum->category->id);
    }

    public function test_forum_has_many_threads(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $forum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Test Forum',
            'slug' => 'test-forum',
        ]);

        $user = User::factory()->create();

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

        $this->assertEquals(2, $forum->threads->count());
        $this->assertTrue($forum->threads->contains($thread1));
        $this->assertTrue($forum->threads->contains($thread2));
    }

    public function test_forum_last_post_relationship(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $forum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Test Forum',
            'slug' => 'test-forum',
        ]);

        $user = User::factory()->create();
        
        $thread = Thread::create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'title' => 'Test Thread',
            'slug' => 'test-thread',
        ]);

        $post = Post::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => 'Test content',
        ]);

        $forum->update(['last_post_id' => $post->id]);
        $forum->refresh();

        $this->assertInstanceOf(Post::class, $forum->lastPost);
        $this->assertEquals($post->id, $forum->lastPost->id);
    }

    public function test_forum_can_be_private(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $publicForum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Public Forum',
            'slug' => 'public-forum',
            'is_private' => false,
        ]);

        $privateForum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Private Forum',
            'slug' => 'private-forum',
            'is_private' => true,
        ]);

        $this->assertFalse($publicForum->is_private);
        $this->assertTrue($privateForum->is_private);
    }
}
