<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KbCategory;
use App\Models\KbArticle;
use App\Models\KbArticleVersion;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;

class KbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check for users
        if (User::count() === 0) {
            $this->command->warn('Please run the DatabaseSeeder first to ensure users exist.');
            return;
        }

        // 1. Create Categories
        $categories = [
            ['name' => 'General FAQs', 'icon' => '💡'],
            ['name' => 'IT Procedures', 'icon' => '🔒'],
            ['name' => 'Hardware Support', 'icon' => '💻'],
            ['name' => 'Software Support', 'icon' => '💽'],
            ['name' => 'Networking', 'icon' => '🌐'],
            ['name' => 'Security', 'icon' => '🛡️'],
        ];

        $categoryModels = collect();
        foreach ($categories as $index => $cat) {
            $categoryModels->push(KbCategory::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
                'sort_order' => $index,
            ]));
        }

        // 2. Create Tags
        $tags = Tag::factory()->count(10)->create();

        // 3. Create Articles
        // Create 20 Public Articles (FAQs)
        $publicArticles = KbArticle::factory()->count(20)->create([
            'visibility' => 'public',
            'status' => 'published',
            'category_id' => $categoryModels->random()->id,
        ]);

        // Create 10 Internal Articles (Procedures)
        $internalArticles = KbArticle::factory()->count(10)->create([
            'visibility' => 'internal',
            'status' => 'published',
            'category_id' => $categoryModels->random()->id,
        ]);
        
        // Create 5 Draft Articles
        $draftArticles = KbArticle::factory()->count(5)->create([
            'status' => 'draft',
            'category_id' => $categoryModels->random()->id,
        ]);

        $allArticles = $publicArticles->concat($internalArticles)->concat($draftArticles);

        // 4. Attach Tags and Versions to Articles
        foreach ($allArticles as $article) {
            // Attach 1-4 random tags
            $article->tags()->attach(
                $tags->random(rand(1, 4))->pluck('id')->toArray()
            );

            // Create 1-3 versions for each article
            $versionsCount = rand(1, 3);
            for ($i = 1; $i <= $versionsCount; $i++) {
                KbArticleVersion::create([
                    'article_id' => $article->id,
                    'version_number' => $i,
                    'title' => $article->title . ($i > 1 ? " (v$i)" : ""),
                    'content' => $article->content . "\n\nUpdated in version $i.",
                    'changed_by' => User::inRandomOrder()->first()->id,
                    'change_summary' => $i === 1 ? 'Initial publish' : 'Updated content and typos',
                ]);
            }
        }
    }
}
