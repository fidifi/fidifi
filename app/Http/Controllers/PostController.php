<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReplyRequest;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function like(Post $post): JsonResponse
    {
        $user = auth()->user();
        
        // Check if user already liked this post
        $existingLike = Like::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();
        
        if ($existingLike) {
            // Unlike the post
            $existingLike->delete();
            $post->decrement('likes_count');
            $liked = false;
        } else {
            // Like the post
            Like::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $post->increment('likes_count');
            $liked = true;
        }
        
        return response()->json([
            'liked' => $liked,
            'likes_count' => $post->fresh()->likes_count,
        ]);
    }

    public function reply(StoreReplyRequest $request, Post $post)
    {
        \Log::info('Reply request received', [
            'post_id' => $post->id,
            'content' => $request->content,
            'quoted_content' => $request->quoted_content,
            'user_id' => auth()->id(),
        ]);
        
        return DB::transaction(function () use ($request, $post) {
            // Load the thread relationship if not already loaded
            $post->load('thread.forum');
            
            // Create the reply post
            $reply = Post::create([
                'thread_id' => $post->thread_id,
                'user_id' => auth()->id(),
                'parent_post_id' => $post->id,
                'content' => $request->content,
                'quoted_content' => $request->quoted_content,
            ]);
            
            \Log::info('Reply created', ['reply_id' => $reply->id]);

            // Update counters
            $thread = $post->thread;
            $forum = $thread->forum;
            
            $thread->increment('posts_count');
            $forum->increment('posts_count');
            auth()->user()->increment('posts_count');

            // Redirect back to the thread
            return redirect()->route('threads.show', [
                'forum' => $forum->slug,
                'thread' => $thread->slug,
            ])->with('success', 'Reply posted successfully');
        });
    }
}
