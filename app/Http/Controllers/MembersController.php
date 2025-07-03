<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MembersController extends Controller
{
    /**
     * Display the members list.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'joined'); // Default sort by join date

        $query = User::query();

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Sorting
        switch ($sort) {
            case 'posts':
                $query->orderBy('posts_count', 'desc');
                break;
            case 'joined':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $members = $query->paginate(20)->withQueryString();

        // Get statistics
        $stats = [
            'total_members' => User::count(),
            'newest_member' => User::latest()->first(),
            'most_posts' => User::orderBy('posts_count', 'desc')->first(),
        ];

        return Inertia::render('Members/Index', [
            'members' => $members,
            'search' => $search,
            'sort' => $sort,
            'stats' => $stats,
        ]);
    }
}