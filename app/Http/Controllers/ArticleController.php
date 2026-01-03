<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /* =========================================================
     |  ADMIN – Dashboard
     ========================================================= */

    /** List ALL articles (dashboard) */
    public function index()
    {
        $articles = Article::orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Articles fetched successfully.',
            'data'    => ['articles' => $articles],
            'errors'  => null,
        ]);
    }

    /** Show single article by ID (dashboard) */
    public function show($id)
    {
        $article = Article::find($id);

        if (! $article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Article fetched successfully.',
            'data'    => $article,
            'errors'  => null,
        ]);
    }

    /** Create article (dashboard) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_ar'     => ['required', 'string', 'max:255'],
            'title_en'     => ['required', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255'],
            'summary_ar'   => ['nullable', 'string'],
            'summary_en'   => ['nullable', 'string'],
            'body_ar'      => ['nullable', 'string'],
            'body_en'      => ['nullable', 'string'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'sort_order'   => ['nullable', 'integer'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        /* Generate unique slug */
        $baseSlug = Str::slug($validated['slug'] ?? $validated['title_en']);
        $slug = $baseSlug;
        $i = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        $validated['slug'] = $slug;

        /* Publish logic */
        if (!empty($validated['is_published'])) {
            $validated['published_at'] = now();
        }

        /* Image upload */
      if ($request->hasFile('image')) {
    $path = $request->file('image')->store('articles', 'public'); // articles/xxx.png
    $validated['image_url'] = basename($path); // xxx.png
}


        $article = Article::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully.',
            'data'    => $article,
            'errors'  => null,
        ], 201);
    }

    /** Update article (dashboard) */
    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (! $article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $validated = $request->validate([
            'title_ar'     => ['sometimes', 'required', 'string', 'max:255'],
            'title_en'     => ['sometimes', 'required', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255', 'unique:articles,slug,' . $article->id],
            'summary_ar'   => ['nullable', 'string'],
            'summary_en'   => ['nullable', 'string'],
            'body_ar'      => ['nullable', 'string'],
            'body_en'      => ['nullable', 'string'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'sort_order'   => ['nullable', 'integer'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        /* Slug regeneration */
        if (array_key_exists('slug', $validated)) {
            $baseSlug = Str::slug($validated['slug'] ?: ($validated['title_en'] ?? $article->title_en));
            $slug = $baseSlug;
            $i = 1;

            while (
                Article::where('slug', $slug)
                    ->where('id', '!=', $article->id)
                    ->exists()
            ) {
                $slug = $baseSlug . '-' . $i++;
            }

            $validated['slug'] = $slug;
        }

        /* Publish logic */
        if (array_key_exists('is_published', $validated)) {
            if ($validated['is_published'] && ! $article->published_at) {
                $validated['published_at'] = now();
            }

            if (! $validated['is_published']) {
                $validated['published_at'] = null;
            }
        }

        /* Image upload */
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('articles', 'public');
            $validated['image_url'] = 'storage/' . $path;
        }

        $article->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully.',
            'data'    => $article,
            'errors'  => null,
        ]);
    }

    /** Delete article (dashboard) */
    public function destroy($id)
    {
        $article = Article::find($id);

        if (! $article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully.',
            'data'    => null,
            'errors'  => null,
        ]);
    }

    /* =========================================================
     |  PUBLIC – Website
     ========================================================= */

    /** List published articles (website) */
    public function publicIndex()
    {
        $articles = Article::where('is_published', true)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->get([
                'id',
                'title_ar',
                'title_en',
                'slug',
                'summary_ar',
                'summary_en',
                'image_url',
                'published_at',
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Published articles fetched successfully.',
            'data'    => ['articles' => $articles],
            'errors'  => null,
        ]);
    }

    /** Show published article by slug (website) */
    public function publicShow($slug)
    {
        $article = Article::where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (! $article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Article fetched successfully.',
            'data'    => $article,
            'errors'  => null,
        ]);
    }


}
