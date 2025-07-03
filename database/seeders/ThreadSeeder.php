<?php

namespace Database\Seeders;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $forums = Forum::all();
        
        foreach ($forums as $forum) {
            // Create 5-15 threads per forum
            $threadCount = rand(5, 15);
            
            for ($i = 0; $i < $threadCount; $i++) {
                $user = $users->random();
                $title = fake()->sentence();
                
                $thread = Thread::create([
                    'forum_id' => $forum->id,
                    'user_id' => $user->id,
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . uniqid(),
                    'is_pinned' => rand(0, 100) < 10, // 10% chance of being pinned
                    'is_locked' => rand(0, 100) < 5,  // 5% chance of being locked
                    'views_count' => rand(0, 1000),
                    'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
                ]);
                
                // Create the first post (thread content)
                $firstPost = Post::create([
                    'thread_id' => $thread->id,
                    'user_id' => $user->id,
                    'content' => fake()->paragraphs(rand(1, 3), true),
                    'created_at' => $thread->created_at,
                ]);
                
                // Create 0-20 replies
                $replyCount = rand(0, 20);
                $lastPost = $firstPost;
                
                for ($j = 0; $j < $replyCount; $j++) {
                    $replyUser = $users->random();
                    $lastPost = Post::create([
                        'thread_id' => $thread->id,
                        'user_id' => $replyUser->id,
                        'content' => fake()->paragraphs(rand(1, 2), true),
                        'likes_count' => rand(0, 10),
                        'created_at' => fake()->dateTimeBetween($thread->created_at, 'now'),
                    ]);
                }
                
                // Update thread stats
                $thread->update([
                    'posts_count' => $replyCount,
                    'last_post_id' => $lastPost->id,
                    'last_post_at' => $lastPost->created_at,
                ]);
                
                // Update forum stats
                $forum->increment('threads_count');
                $forum->increment('posts_count', $replyCount + 1);
                $forum->update(['last_post_id' => $lastPost->id]);
                
                // Update user stats
                $user->increment('threads_count');
                $user->increment('posts_count');
            }
        }
    }
}
