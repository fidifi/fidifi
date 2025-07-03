<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $user = User::factory()->create(['name' => 'John Doe', 'username' => 'johndoe']);
        $category = Category::factory()->create(['name' => 'Programming']);
        $forum = Forum::factory()->create(['name' => 'Laravel Discussion', 'category_id' => $category->id]);
        
        Thread::factory()->create([
            'title' => 'How to use Laravel Eloquent',
            'forum_id' => $forum->id,
            'user_id' => $user->id,
        ]);
        
        Thread::factory()->create([
            'title' => 'Vue.js best practices',
            'forum_id' => $forum->id,
            'user_id' => $user->id,
        ]);
        
        $thread = Thread::factory()->create([
            'title' => 'General discussion',
            'forum_id' => $forum->id,
            'user_id' => $user->id,
        ]);
        
        Post::factory()->create([
            'content' => 'Laravel is a great framework for building web applications',
            'thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_search_page_can_be_rendered(): void
    {
        $response = $this->get('/search');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Search/Index'));
    }

    public function test_can_search_threads_by_title(): void
    {
        $response = $this->get('/search?q=Laravel');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.threads', 1)
            ->where('results.threads.0.title', 'How to use Laravel Eloquent')
        );
    }

    public function test_can_search_posts_by_content(): void
    {
        $response = $this->get('/search?q=framework');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.posts', 1)
            ->where('results.posts.0.content', 'Laravel is a great framework for building web applications')
        );
    }

    public function test_can_search_users_by_name(): void
    {
        $response = $this->get('/search?q=John');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.users', 1)
            ->where('results.users.0.name', 'John Doe')
        );
    }

    public function test_can_search_users_by_username(): void
    {
        $response = $this->get('/search?q=johndoe');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.users', 1)
            ->where('results.users.0.username', 'johndoe')
        );
    }

    public function test_search_returns_empty_results_for_no_matches(): void
    {
        $response = $this->get('/search?q=nonexistent');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.threads', 0)
            ->has('results.posts', 0)
            ->has('results.users', 0)
        );
    }

    public function test_search_requires_query_parameter(): void
    {
        $response = $this->get('/search');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->where('query', '')
            ->where('results', null)
        );
    }

    public function test_search_filters_by_type(): void
    {
        $response = $this->get('/search?q=Eloquent&type=threads');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.threads.data', 1)
            ->missing('results.posts')
            ->missing('results.users')
        );
    }

    public function test_search_paginates_results(): void
    {
        // Create many threads
        Thread::factory(25)->create([
            'title' => 'Laravel Tutorial',
            'forum_id' => Forum::first()->id,
            'user_id' => User::first()->id,
        ]);

        $response = $this->get('/search?q=Tutorial&type=threads');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.threads.data', 20) // Default pagination
            ->has('results.threads.data.0.id')
        );
    }

    public function test_search_filters_by_posts_type(): void
    {
        $response = $this->get('/search?q=framework&type=posts');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.posts.data', 1)
            ->where('results.posts.data.0.content', 'Laravel is a great framework for building web applications')
            ->missing('results.threads')
            ->missing('results.users')
        );
    }

    public function test_search_filters_by_users_type(): void
    {
        $response = $this->get('/search?q=John&type=users');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.users.data', 1)
            ->where('results.users.data.0.name', 'John Doe')
            ->missing('results.threads')
            ->missing('results.posts')
        );
    }

    public function test_search_posts_paginates_results(): void
    {
        // Create many posts with the search term
        $thread = Thread::first();
        Post::factory(25)->create([
            'content' => 'This is a test post with framework content',
            'thread_id' => $thread->id,
            'user_id' => User::first()->id,
        ]);

        $response = $this->get('/search?q=framework&type=posts');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.posts.data', 20) // Default pagination
            ->has('results.posts.data.0.id')
        );
    }

    public function test_search_users_paginates_results(): void
    {
        // Create many users with the search term
        User::factory(25)->create([
            'name' => 'Test User',
        ]);

        $response = $this->get('/search?q=Test&type=users');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.users.data', 20) // Default pagination
            ->has('results.users.data.0.id')
        );
    }

    public function test_search_users_by_username_with_type_filter(): void
    {
        User::factory()->create([
            'name' => 'Different Name',
            'username' => 'specialuser',
        ]);

        $response = $this->get('/search?q=specialuser&type=users');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Search/Index')
            ->has('results.users.data', 1)
            ->where('results.users.data.0.username', 'specialuser')
            ->missing('results.threads')
            ->missing('results.posts')
        );
    }
}