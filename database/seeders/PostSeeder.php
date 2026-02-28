<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\PostStatus;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->warn('No users found. Please create a user first.');
            return;
        }

        $posts = [
            [
                'title' => 'Welcome to Our Blog',
                'excerpt' => 'This is the first post on our new blog platform. Learn about what we have planned.',
                'content' => "Welcome to our brand new blog! We're excited to share our thoughts, ideas, and experiences with you.\n\nIn this space, you'll find articles about technology, development, and much more. We believe in sharing knowledge and helping others grow.\n\nStay tuned for more exciting content coming your way!",
                'status' => PostStatus::PUBLISHED,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Getting Started with Laravel',
                'excerpt' => 'A comprehensive guide to building modern web applications with Laravel framework.',
                'content' => "Laravel is one of the most popular PHP frameworks for building modern web applications. It provides an elegant syntax and powerful tools that make development a joy.\n\nKey features include:\n- Eloquent ORM for database interactions\n- Blade templating engine\n- Built-in authentication\n- Queue management\n- And much more!\n\nWhether you're building a small project or a large enterprise application, Laravel has you covered.",
                'status' => PostStatus::PUBLISHED,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'The Power of Livewire',
                'excerpt' => 'Discover how Livewire makes building dynamic interfaces in Laravel incredibly simple.',
                'content' => "Livewire is a full-stack framework for Laravel that makes building dynamic interfaces simple, without leaving the comfort of Laravel.\n\nWith Livewire, you can:\n- Build reactive components\n- Handle real-time validation\n- Create dynamic forms\n- Implement pagination easily\n\nIt's like having the power of a JavaScript framework, but with the simplicity of server-side rendering.",
                'status' => PostStatus::PUBLISHED,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Understanding Database Design',
                'excerpt' => 'Best practices for designing scalable and efficient database schemas.',
                'content' => "Good database design is crucial for building scalable applications. Here are some key principles:\n\n1. Normalize your data to reduce redundancy\n2. Use appropriate data types\n3. Index frequently queried columns\n4. Consider relationships carefully\n5. Plan for growth\n\nA well-designed database will serve your application well for years to come.",
                'status' => PostStatus::DRAFT,
                'published_at' => null,
            ],
            [
                'title' => 'Modern CSS Techniques',
                'excerpt' => 'Exploring the latest CSS features and how to use them effectively.',
                'content' => "CSS has evolved tremendously in recent years. Modern CSS gives us powerful tools like:\n\n- Flexbox for flexible layouts\n- Grid for complex layouts\n- Custom properties (CSS variables)\n- Container queries\n- And much more!\n\nThese features make it easier than ever to create beautiful, responsive designs.",
                'status' => PostStatus::DRAFT,
                'published_at' => null,
            ],
        ];

        foreach ($posts as $postData) {
            Post::create([
                'user_id' => $user->id,
                'title' => $postData['title'],
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'status' => $postData['status'],
                'published_at' => $postData['published_at'],
            ]);
        }

        $this->command->info('Sample blog posts created successfully!');
    }
}
