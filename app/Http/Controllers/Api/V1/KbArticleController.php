<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\KbArticle;
use App\Models\KbArticleVersion;
use Illuminate\Http\Request;
use App\Http\Requests\StoreKbArticleRequest;
use App\Http\Requests\UpdateKbArticleRequest;
use Illuminate\Support\Str;

class KbArticleController extends Controller
{
    /**
     * Search and list articles adhering to visibility.
     */
    public function index(Request $request)
    {
        $query = KbArticle::with(['category', 'tags', 'author']);

        // End users only see published & public
        if ($request->user() && $request->user()->role === 'user') {
            $query->where('visibility', 'public')
                  ->where('status', 'published');
        } else {
            // Agents/Admins can filter by status/visibility explicitly
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('visibility')) {
                $query->where('visibility', $request->visibility);
            }
        }

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                // MySQL FullText Search
                $q->whereRaw('MATCH(title, content) AGAINST(? IN BOOLEAN MODE)', [$searchTerm])
                  ->orWhere('title', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $articles = $query->latest('updated_at')->paginate(15);

        return response()->json([
            'data' => $articles->items(),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
            'message' => 'Articles retrieved successfully',
        ], 200);
    }

    /**
     * Suggest articles for ticket creation.
     */
    public function suggest(Request $request)
    {
        $searchTerm = $request->q;
        if (!$searchTerm) {
            return response()->json(['data' => []], 200);
        }

        $query = KbArticle::with(['category'])->where('status', 'published');

        if ($request->user() && $request->user()->role === 'user') {
            $query->where('visibility', 'public');
        }

        $articles = $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('MATCH(title, content) AGAINST(? IN BOOLEAN MODE)', [$searchTerm])
                  ->orWhere('title', 'LIKE', "%{$searchTerm}%");
            })
            ->take(3)
            ->get();

        return response()->json([
            'data' => $articles,
            'message' => 'Suggestions retrieved successfully',
        ], 200);
    }

    /**
     * Display a single article.
     */
    public function show(Request $request, $slug)
    {
        $article = KbArticle::with(['category', 'tags', 'author'])->where('slug', $slug)->firstOrFail();

        if ($request->user() && $request->user()->role === 'user') {
            if ($article->visibility !== 'public' || $article->status !== 'published') {
                abort(403, 'Unauthorized access to this article.');
            }
        }

        $article->increment('view_count');

        return response()->json([
            'data' => $article,
            'message' => 'Article retrieved successfully',
        ], 200);
    }

    /**
     * Provide feedback on an article.
     */
    public function feedback(Request $request, $slug)
    {
        $request->validate([
            'helpful' => 'required|boolean'
        ]);

        $article = KbArticle::where('slug', $slug)->firstOrFail();

        if ($request->helpful) {
            $article->increment('helpful_yes');
        } else {
            $article->increment('helpful_no');
        }

        return response()->json([
            'message' => 'Feedback submitted successfully',
        ], 200);
    }

    /**
     * List article versions.
     */
    public function versions(Request $request, $slug)
    {
        $article = KbArticle::where('slug', $slug)->firstOrFail();

        // Agents or Admins only
        if ($request->user() && $request->user()->role === 'user') {
            abort(403, 'Unauthorized');
        }

        $versions = $article->versions()->with('editor')->get();

        return response()->json([
            'data' => $versions,
            'message' => 'Versions retrieved successfully',
        ], 200);
    }

    /**
     * Store a new article.
     */
    public function store(StoreKbArticleRequest $request)
    {
        $validated = $request->validated();
        $validated['author_id'] = $request->user()->id;
        $validated['slug'] = Str::slug($validated['title']);

        // Check uniqueness of slug
        if (KbArticle::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $validated['slug'] . '-' . Str::random(5);
        }

        $article = KbArticle::create($validated);

        if (isset($validated['tags']) && is_array($validated['tags'])) {
            $article->tags()->sync($validated['tags']);
        }

        return response()->json([
            'data' => $article->load(['category', 'tags', 'author']),
            'message' => 'Article created successfully',
        ], 201);
    }

    /**
     * Update an article.
     */
    public function update(UpdateKbArticleRequest $request, $slug)
    {
        $article = KbArticle::where('slug', $slug)->firstOrFail();
        $validated = $request->validated();

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
            if (KbArticle::where('slug', $validated['slug'])->where('id', '!=', $article->id)->exists()) {
                $validated['slug'] = $validated['slug'] . '-' . Str::random(5);
            }
        }

        // Store version before updating
        $latestVersion = $article->versions()->max('version_number') ?? 0;
        KbArticleVersion::create([
            'article_id' => $article->id,
            'version_number' => $latestVersion + 1,
            'title' => $article->title,
            'content' => $article->content,
            'changed_by' => $request->user()->id,
            'change_summary' => $validated['change_summary'] ?? 'Article updated',
        ]);

        $article->update($validated);

        if (isset($validated['tags']) && is_array($validated['tags'])) {
            $article->tags()->sync($validated['tags']);
        }

        return response()->json([
            'data' => $article->load(['category', 'tags', 'author']),
            'message' => 'Article updated successfully',
        ], 200);
    }

    /**
     * Delete an article.
     */
    public function destroy(Request $request, $slug)
    {
        $article = KbArticle::where('slug', $slug)->firstOrFail();
        
        if ($request->user() && $request->user()->role === 'user') {
            abort(403, 'Unauthorized');
        }

        $article->delete();

        return response()->json([
            'message' => 'Article deleted successfully',
        ], 200);
    }
}
