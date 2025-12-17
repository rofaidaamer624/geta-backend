<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * GET /api/admin/languages
     */
    public function index()
    {
        $languages = Language::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Languages fetched successfully.',
            'data'    => [
                'languages' => $languages,
            ],
            'errors'  => null,
        ]);
    }

    /**
     * GET /api/admin/languages/{id}
     */
    public function show($id)
    {
        $language = Language::find($id);

        if (! $language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Language fetched successfully.',
            'data'    => $language,
            'errors'  => null,
        ]);
    }

    /**
     * POST /api/admin/languages
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar'    => ['required', 'string', 'max:255'],
            'name_en'    => ['required', 'string', 'max:255'],
            'code'       => ['required', 'string', 'max:10', 'unique:languages,code'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $language = Language::create([
            'name_ar'    => $validated['name_ar'],
            'name_en'    => $validated['name_en'],
            'code'       => $validated['code'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Language created successfully.',
            'data'    => $language,
            'errors'  => null,
        ], 201);
    }

    /**
     * POST /api/admin/languages/{id}
     * أو PUT /api/admin/languages/{id}
     */
    public function update(Request $request, $id)
    {
        $language = Language::find($id);

        if (! $language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $validated = $request->validate([
            'name_ar'    => ['required', 'string', 'max:255'],
            'name_en'    => ['required', 'string', 'max:255'],
            'code'       => ['required', 'string', 'max:10', "unique:languages,code,{$language->id}"],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $language->update([
            'name_ar'    => $validated['name_ar'],
            'name_en'    => $validated['name_en'],
            'code'       => $validated['code'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Language updated successfully.',
            'data'    => $language,
            'errors'  => null,
        ]);
    }

    /**
     * DELETE /api/admin/languages/{id}
     */
    public function destroy($id)
    {
        $language = Language::find($id);

        if (! $language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $language->delete();

        return response()->json([
            'success' => true,
            'message' => 'Language deleted successfully.',
            'data'    => null,
            'errors'  => null,
        ]);
    }

    /**
     * GET /api/languages  (للـ front)
     */
    public function publicIndex()
    {
        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Languages fetched successfully.',
            'data'    => [
                'languages' => $languages,
            ],
            'errors'  => null,
        ]);
    }
}
