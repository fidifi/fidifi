<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThreadRequest;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ThreadController extends Controller
{
    /**
     * Show the form for creating a new thread.
     */
    public function create(Forum $forum): Response
    {
        return Inertia::render('Threads/Create', [
            'forum' => $forum,
        ]);
    }

    /**
     * Store a newly created thread in storage.
     */
    public function store(StoreThreadRequest $request, Forum $forum): RedirectResponse
    {
        return DB::transaction(function () use ($request, $forum) {
            // Create the thread
            $thread = Thread::create([
                'forum_id' => $forum->id,
                'user_id' => auth()->id(),
                'title' => $request->title,
                'slug' => Str::slug($request->title),
            ]);

            // Create the initial post
            Post::create([
                'thread_id' => $thread->id,
                'user_id' => auth()->id(),
                'content' => $request->content,
            ]);

            // Update counters
            $forum->increment('threads_count');
            $forum->increment('posts_count');
            $thread->increment('posts_count');
            auth()->user()->increment('threads_count');
            auth()->user()->increment('posts_count');

            return redirect()->route('threads.show', [
                'forum' => $forum->slug,
                'thread' => $thread->slug,
            ]);
        });
    }

    /**
     * Display the specified thread.
     */
    public function show(Forum $forum, Thread $thread): Response
    {
        // Increment views
        $thread->increment('views_count');
        
        $thread->load(['user', 'forum.category']);
        
        $posts = $thread->posts()
            ->with('user')
            ->orderBy('created_at')
            ->paginate(20);

        // Add like information to posts
        if (auth()->check()) {
            $posts->getCollection()->transform(function ($post) {
                $post->is_liked_by_user = $post->likes()
                    ->where('user_id', auth()->id())
                    ->exists();
                return $post;
            });
        } else {
            $posts->getCollection()->transform(function ($post) {
                $post->is_liked_by_user = false;
                return $post;
            });
        }

        return Inertia::render('Threads/Show', [
            'forum' => $forum,
            'thread' => $thread,
            'posts' => $posts,
        ]);
    }
}
