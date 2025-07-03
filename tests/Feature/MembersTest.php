<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users with different join dates and post counts
        User::factory()->create([
            'name' => 'Alice Johnson',
            'username' => 'alice',
            'email' => 'alice@example.com',
            'posts_count' => 150,
            'created_at' => now()->subDays(30),
        ]);
        
        User::factory()->create([
            'name' => 'Bob Smith',
            'username' => 'bob',
            'email' => 'bob@example.com',
            'posts_count' => 75,
            'created_at' => now()->subDays(15),
        ]);
        
        User::factory()->create([
            'name' => 'Charlie Brown',
            'username' => 'charlie',
            'email' => 'charlie@example.com',
            'posts_count' => 200,
            'created_at' => now()->subDays(60),
        ]);
    }

    public function test_members_page_can_be_rendered(): void
    {
        $response = $this->get('/members');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Members/Index'));
    }

    public function test_members_are_paginated(): void
    {
        $response = $this->get('/members');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->has('members.data', 3)
            ->has('members.current_page')
            ->has('members.last_page')
        );
    }

    public function test_members_can_be_sorted_by_posts_count(): void
    {
        $response = $this->get('/members?sort=posts');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->where('members.data.0.posts_count', 200) // Charlie should be first
            ->where('members.data.1.posts_count', 150) // Alice should be second
            ->where('members.data.2.posts_count', 75)  // Bob should be third
        );
    }

    public function test_members_can_be_sorted_by_join_date(): void
    {
        $response = $this->get('/members?sort=joined');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->where('members.data.0.username', 'bob')     // Most recent
            ->where('members.data.1.username', 'alice')   // Middle
            ->where('members.data.2.username', 'charlie') // Oldest
        );
    }

    public function test_members_default_sort_is_by_join_date(): void
    {
        $response = $this->get('/members');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->where('members.data.0.username', 'bob') // Most recent by default
        );
    }

    public function test_members_can_be_searched(): void
    {
        $response = $this->get('/members?search=alice');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->has('members.data', 1)
            ->where('members.data.0.username', 'alice')
        );
    }

    public function test_members_search_works_with_username(): void
    {
        $response = $this->get('/members?search=charlie');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->has('members.data', 1)
            ->where('members.data.0.name', 'Charlie Brown')
        );
    }

    public function test_members_search_returns_empty_for_no_matches(): void
    {
        $response = $this->get('/members?search=nonexistent');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->has('members.data', 0)
        );
    }

    public function test_members_displays_user_stats(): void
    {
        $response = $this->get('/members');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->has('stats.total_members')
            ->has('stats.newest_member')
            ->has('stats.most_posts')
        );
    }
}