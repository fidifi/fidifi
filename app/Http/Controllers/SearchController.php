<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    /**
     * Display the search page and results.
     */
    public function index(Request $request): Response
    {
        $query = $request->input('q', '');
        $type = $request->input('type');
        $results = null;

        if ($query) {
            $results = match ($type) {
                'threads' => ['threads' => $this->searchThreads($query)],
                'posts' => ['posts' => $this->searchPosts($query)],
                'users' => ['users' => $this->searchUsers($query)],
                default => $this->searchAll($query),
            };
        }

        return Inertia::render('Search/Index', [
            'query' => $query,
            'type' => $type,
            'results' => $results,
        ]);
    }

    /**
     * Search only threads.
     */
    private function searchThreads(string $query)
    {
        return Thread::with(['user', 'forum'])
            ->where('title', 'like', "%{$query}%")
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Search only posts.
     */
    private function searchPosts(string $query)
    {
        return Post::with(['user', 'thread.forum'])
            ->where('content', 'like', "%{$query}%")
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Search only users.
     */
    private function searchUsers(string $query)
    {
        return User::where('name', 'like', "%{$query}%")
            ->orWhere('username', 'like', "%{$query}%")
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Search all types.
     */
    private function searchAll(string $query): array
    {
        $threads = Thread::with(['user', 'forum'])
            ->where('title', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        $posts = Post::with(['user', 'thread.forum'])
            ->where('content', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('username', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return [
            'threads' => $threads,
            'posts' => $posts,
            'users' => $users,
        ];
    }
}