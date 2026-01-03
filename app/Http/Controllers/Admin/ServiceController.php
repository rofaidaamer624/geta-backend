<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order')->orderBy('id')->get();

        foreach ($services as $s) {
            $s->icon_url = $s->icon_path ? asset('storage/' . $s->icon_path) : null;
        }

        return response()->json([
            'success' => true,
            'message' => 'Services fetched successfully.',
            'data'    => ['services' => $services],
            'errors'  => null,
        ]);
    }

    public function show($id)
    {
        $service = Service::find($id);
        if (! $service) {
            return response()->json(['success' => false, 'message' => 'Service not found.'], 404);
        }

        $service->icon_url = $service->icon_path ? asset('storage/' . $service->icon_path) : null;

        return response()->json([
            'success' => true,
            'message' => 'Service fetched successfully.',
            'data'    => $service,
            'errors'  => null,
        ]);
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'name_ar'               => ['required', 'string', 'max:255'],
        'name_en'               => ['required', 'string', 'max:255'],

        // 'short_description_ar'  => ['nullable', 'string', 'max:255'],
        // 'short_description_en'  => ['nullable', 'string', 'max:255'],

        'description_ar'        => ['nullable', 'string'],
        'description_en'        => ['nullable', 'string'],

        'price_text'            => ['nullable', 'string', 'max:255'],
        'sort_order'            => ['nullable', 'integer', 'min:0'],
        'category'              => ['required', 'in:translation,academic,religious'],

        'icon'                  => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
    ]);

    $iconPath = $request->hasFile('icon')
        ? $request->file('icon')->store('services', 'public')
        : null;

    $service = Service::create([
        'name_ar'               => $validated['name_ar'],
        'name_en'               => $validated['name_en'],
        'slug'                  => $request->input('slug'),

        // 'short_description_ar'  => $validated['short_description_ar'] ?? null,
        // 'short_description_en'  => $validated['short_description_en'] ?? null,

        'description_ar'        => $validated['description_ar'] ?? null,
        'description_en'        => $validated['description_en'] ?? null,

        'price_text'            => $validated['price_text'] ?? null,
        'sort_order'            => $validated['sort_order'] ?? 0,
        'category'              => $validated['category'],

        'icon_path'             => $iconPath,
    ]);

    $service->icon_url = $service->icon_path ? asset('storage/' . $service->icon_path) : null;

    return response()->json([
        'success' => true,
        'message' => 'Service created successfully.',
        'data'    => $service,
        'errors'  => null,
    ], 201);
}


public function update(Request $request, $id)
{
    $service = Service::find($id);
    if (! $service) {
        return response()->json(['success' => false, 'message' => 'Service not found.'], 404);
    }

    $validated = $request->validate([
        'name_ar'               => ['required', 'string', 'max:255'],
        'name_en'               => ['required', 'string', 'max:255'],

        // 'short_description_ar'  => ['nullable', 'string', 'max:255'],
        // 'short_description_en'  => ['nullable', 'string', 'max:255'],

        'description_ar'        => ['nullable', 'string'],
        'description_en'        => ['nullable', 'string'],

        'price_text'            => ['nullable', 'string', 'max:255'],
        'sort_order'            => ['nullable', 'integer', 'min:0'],
        'category'              => ['required', 'in:translation,academic,religious'],

        'icon'                  => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
    ]);

    if ($request->hasFile('icon')) {
        if ($service->icon_path && file_exists(public_path('storage/' . $service->icon_path))) {
            @unlink(public_path('storage/' . $service->icon_path));
        }
        $service->icon_path = $request->file('icon')->store('services', 'public');
    }

    $service->fill($validated);

    if ($request->filled('slug')) {
        $service->slug = $request->input('slug');
    }

    $service->save();

    $service->icon_url = $service->icon_path ? asset('storage/' . $service->icon_path) : null;

    return response()->json([
        'success' => true,
        'message' => 'Service updated successfully.',
        'data'    => $service,
        'errors'  => null,
    ]);
}


    public function destroy($id)
    {
        $service = Service::find($id);
        if (! $service) {
            return response()->json(['success' => false, 'message' => 'Service not found.'], 404);
        }

        if ($service->icon_path && file_exists(public_path('storage/' . $service->icon_path))) {
            @unlink(public_path('storage/' . $service->icon_path));
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.',
        ]);
    }

public function publicIndex()
{
    $services = Service::orderBy('sort_order')
        ->orderBy('id')
        ->get();

    foreach ($services as $s) {
        $s->icon_url = $s->icon_path ? asset('storage/' . $s->icon_path) : null;
    }

    return response()->json([
        'success' => true,
        'message' => 'Services fetched successfully.',
        'data'    => ['services' => $services],
        'errors'  => null,
    ]);
}


}
