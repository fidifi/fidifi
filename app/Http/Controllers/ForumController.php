<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Forum;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ForumController extends Controller
{
    public function index(): Response
    {
        $categories = Category::with(['forums' => function ($query) {
            $query->withCount('threads')
                ->with(['lastPost' => function ($query) {
                    $query->with(['user', 'thread']);
                }]);
        }])
        ->orderBy('order')
        ->get();

        return Inertia::render('Forums/Index', [
            'categories' => $categories,
        ]);
    }

    public function show(Forum $forum): Response
    {
        $forum->load('category');
        
        $threads = $forum->threads()
            ->with(['user', 'lastPost.user'])
            ->withCount('posts')
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_post_at')
            ->paginate(20);

        return Inertia::render('Forums/Show', [
            'forum' => $forum,
            'threads' => $threads,
        ]);
    }
}
