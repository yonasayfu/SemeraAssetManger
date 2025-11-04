<?php

namespace Database\Seeders;

use App\Models\SupportPage;
use Illuminate\Database\Seeder;

class SupportPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About Us',
                'excerpt' => 'Learn about the product and team.',
                'content' => '<p>Welcome to Asset Management. This page describes our mission and the team behind the product.</p>',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'excerpt' => 'How to reach our support team.',
                'content' => '<p>Need help? Email support@example.com or call +1-555-0100.</p>',
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms of Service',
                'excerpt' => 'The rules for using this service.',
                'content' => '<p>These terms govern your use of the application. Replace with your legal copy.</p>',
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'excerpt' => 'How we handle your data.',
                'content' => '<p>We respect your privacy. Add your policy text here.</p>',
            ],
            [
                'slug' => 'videos',
                'title' => 'Videos',
                'excerpt' => 'Tutorials and product tours.',
                'content' => '<p>Embed training videos and product walkthroughs here.</p>',
            ],
            [
                'slug' => 'reviews',
                'title' => 'User Reviews',
                'excerpt' => 'What people are saying.',
                'content' => '<p>Showcase testimonials and ratings.</p>',
            ],
            [
                'slug' => 'changelog',
                'title' => 'Changelog',
                'excerpt' => 'Release notes and updates.',
                'content' => '<p>Track new features, bug fixes, and improvements.</p>',
            ],
        ];

        foreach ($pages as $page) {
            SupportPage::updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'excerpt' => $page['excerpt'],
                    'content' => $page['content'],
                    'published' => true,
                ]
            );
        }
    }
}

