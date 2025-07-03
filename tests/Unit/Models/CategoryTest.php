<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Forum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_fillable_attributes(): void
    {
        $category = new Category();
        
        $this->assertEquals([
            'name',
            'slug',
            'description',
            'color',
            'order',
        ], $category->getFillable());
    }

    public function test_category_casts_order_to_integer(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'order' => '5',
        ]);

        $this->assertIsInt($category->order);
        $this->assertEquals(5, $category->order);
    }

    public function test_category_has_many_forums(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $forum1 = Forum::create([
            'category_id' => $category->id,
            'name' => 'Forum 1',
            'slug' => 'forum-1',
            'order' => 1,
        ]);

        $forum2 = Forum::create([
            'category_id' => $category->id,
            'name' => 'Forum 2',
            'slug' => 'forum-2',
            'order' => 0,
        ]);

        $this->assertEquals(2, $category->forums->count());
        $this->assertTrue($category->forums->contains($forum1));
        $this->assertTrue($category->forums->contains($forum2));
    }

    public function test_category_forums_are_ordered_by_order_field(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $forum2 = Forum::create([
            'category_id' => $category->id,
            'name' => 'Second Forum',
            'slug' => 'second-forum',
            'order' => 2,
        ]);

        $forum1 = Forum::create([
            'category_id' => $category->id,
            'name' => 'First Forum',
            'slug' => 'first-forum',
            'order' => 1,
        ]);

        $forums = $category->forums;
        
        $this->assertEquals('First Forum', $forums->first()->name);
        $this->assertEquals('Second Forum', $forums->last()->name);
    }

    public function test_category_can_be_created_with_all_attributes(): void
    {
        $category = Category::create([
            'name' => 'Development',
            'slug' => 'development',
            'description' => 'Programming discussions',
            'color' => '#ff0000',
            'order' => 10,
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Development',
            'slug' => 'development',
            'description' => 'Programming discussions',
            'color' => '#ff0000',
            'order' => 10,
        ]);
    }
}
