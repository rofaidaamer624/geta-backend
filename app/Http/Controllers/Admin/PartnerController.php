<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    /* =========================================================
     |  ADMIN – Dashboard
     ========================================================= */

    /** List ALL partners (dashboard) */
    public function index()
    {
        $partners = Partner::orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Partners fetched successfully.',
            'data'    => ['partners' => $partners],
            'errors'  => null,
        ]);
    }

    /** Show single partner by ID (dashboard) */
    public function show($id)
    {
        $partner = Partner::find($id);

        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Partner fetched successfully.',
            'data'    => $partner,
            'errors'  => null,
        ]);
    }

    /** Create partner (dashboard) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
            'logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg'],
        ]);

       if ($request->hasFile('logo')) {
    $path = $request->file('logo')->store('partners', 'public'); // partners/xxx.png
    $validated['logo_path'] = basename($path);                   // xxx.png فقط
}

        $partner = Partner::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Partner created successfully.',
            'data'    => $partner,
            'errors'  => null,
        ], 201);
    }

    /** Update partner (dashboard) */
    public function update(Request $request, $id)
    {
        $partner = Partner::find($id);

        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $validated = $request->validate([
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
            'logo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg'],
        ]);

        if ($request->hasFile('logo')) {
            if ($partner->logo_path) {
                Storage::disk('public')->delete($partner->logo_path);
            }

            $validated['logo_path'] =
                $request->file('logo')->store('partners', 'public');
        }

        $partner->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Partner updated successfully.',
            'data'    => $partner,
            'errors'  => null,
        ]);
    }

    /** Delete partner (dashboard) */
    public function destroy($id)
    {
        $partner = Partner::find($id);

        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        if ($partner->logo_path) {
            Storage::disk('public')->delete($partner->logo_path);
        }

        $partner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Partner deleted successfully.',
            'data'    => null,
            'errors'  => null,
        ]);
    }

    /* =========================================================
     |  PUBLIC – Website
     ========================================================= */

    /** List active partners (website) */
    public function publicIndex()
    {
       Partner::orderBy('sort_order')->orderBy('id')->get();
        $partners = Partner::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Active partners fetched successfully.',
            'data'    => ['partners' => $partners],
            'errors'  => null,
        ]);
    }
}
