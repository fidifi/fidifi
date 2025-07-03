<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Seeder;

class CypressTestSeeder extends Seeder
{
    /**
     * Seed the database with predictable test data for Cypress tests.
     */
    public function run(): void
    {
        // Create a test category
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Category for Cypress testing',
            'order' => 1,
        ]);

        // Create a test forum
        $forum = Forum::create([
            'category_id' => $category->id,
            'name' => 'Test Forum',
            'slug' => 'test-forum',
            'description' => 'Forum for Cypress testing',
            'order' => 1,
            'threads_count' => 0,
            'posts_count' => 0,
        ]);

        // Create test users
        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'threads_count' => 0,
            'posts_count' => 0,
        ]);

        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'threads_count' => 0,
            'posts_count' => 0,
        ]);

        // Create a test thread
        $thread = Thread::create([
            'forum_id' => $forum->id,
            'user_id' => $testUser->id,
            'title' => 'Test Thread for Interactions',
            'slug' => 'test-thread-for-interactions',
            'views_count' => 0,
            'posts_count' => 0,
            'is_locked' => false,
            'is_pinned' => false,
        ]);

        // Create the initial post
        $post = Post::create([
            'thread_id' => $thread->id,
            'user_id' => $testUser->id,
            'content' => 'This is a test post for interactions. Users should be able to like, quote, and reply to this post.',
            'likes_count' => 0,
        ]);

        // Update counters
        $forum->update([
            'threads_count' => 1,
            'posts_count' => 1,
        ]);

        $thread->update([
            'posts_count' => 1,
        ]);

        $testUser->update([
            'threads_count' => 1,
            'posts_count' => 1,
        ]);
    }
}