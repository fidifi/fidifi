<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Thread;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ThreadController extends Controller
{
    public function show(Forum $forum, Thread $thread): Response
    {
        // Increment views
        $thread->increment('views_count');
        
        $thread->load(['user', 'forum.category']);
        
        $posts = $thread->posts()
            ->with('user')
            ->orderBy('created_at')
            ->paginate(20);

        return Inertia::render('Threads/Show', [
            'forum' => $forum,
            'thread' => $thread,
            'posts' => $posts,
        ]);
    }
}
