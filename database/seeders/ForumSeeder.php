<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Forum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'General Discussion',
                'description' => 'Talk about anything and everything',
                'color' => '#6366f1',
                'forums' => [
                    ['name' => 'Announcements', 'description' => 'Official announcements from the FidiFi team'],
                    ['name' => 'General Chat', 'description' => 'Casual conversations about any topic'],
                    ['name' => 'Introductions', 'description' => 'New here? Introduce yourself!'],
                ]
            ],
            [
                'name' => 'Support',
                'description' => 'Get help and support',
                'color' => '#10b981',
                'forums' => [
                    ['name' => 'Technical Support', 'description' => 'Having technical issues? We can help'],
                    ['name' => 'Feature Requests', 'description' => 'Suggest new features for FidiFi'],
                    ['name' => 'Bug Reports', 'description' => 'Found a bug? Let us know'],
                ]
            ],
            [
                'name' => 'Development',
                'description' => 'Programming and development discussions',
                'color' => '#f59e0b',
                'forums' => [
                    ['name' => 'Web Development', 'description' => 'HTML, CSS, JavaScript, and more'],
                    ['name' => 'Backend Development', 'description' => 'Server-side programming discussions'],
                    ['name' => 'Mobile Development', 'description' => 'iOS, Android, and cross-platform development'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $forums = $categoryData['forums'];
            unset($categoryData['forums']);
            
            $category = Category::create([
                ...$categoryData,
                'slug' => Str::slug($categoryData['name']),
                'order' => Category::count(),
            ]);

            foreach ($forums as $forumData) {
                Forum::create([
                    ...$forumData,
                    'category_id' => $category->id,
                    'slug' => Str::slug($forumData['name']),
                    'order' => Forum::where('category_id', $category->id)->count(),
                ]);
            }
        }
    }
}
