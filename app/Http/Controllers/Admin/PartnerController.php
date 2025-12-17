<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * API لإضافة شراكة جديدة
     * POST /api/admin/partners
     */


    public function index()
{
    // نجيب كل الشركاء مرتبين
    $partners = Partner::orderBy('sort_order')
        ->orderBy('id')
        ->get();

    // نضيف logo_url جاهز لكل واحد
    foreach ($partners as $partner) {
        $partner->logo_url = $partner->logo_path
            ? asset('storage/' . $partner->logo_path)
            : null;
    }

    return response()->json([
        'success' => true,
        'message' => 'Partners fetched successfully.',
        'data'    => [
            'partners' => $partners,
        ],
        'errors'  => null,
    ]);
}

    public function store(Request $request)
    {
        // 1) Validation
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'logo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ]);

        // 2) رفع الصورة (لو موجودة)
        $logoPath = null;
        if ($request->hasFile('logo')) {
            // التخزين في storage/app/public/partners
            $logoPath = $request->file('logo')->store('partners', 'public');
        }

        // 3) إنشاء الريكورد في الداتابيز
        $partner = Partner::create([
            'name'        => $validated['name'],
            'website_url' => $validated['website_url'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'logo_path'   => $logoPath,
        ]);

        // 4) تجهيز اللوجو كرابط كامل
        $partner->logo_url = $partner->logo_path
            ? asset('storage/' . $partner->logo_path)
            : null;

        return response()->json([
            'success' => true,
            'message' => 'Partner created successfully.',
            'data'    => $partner,
            'errors'  => null,
        ], 201);
    }
    /**
 * API لتحديث شراكة موجودة
 * POST /api/admin/partners/{id}
 */
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

    // Validation
    $validated = $request->validate([
        'name'        => ['required', 'string', 'max:255'],
        'website_url' => ['nullable', 'url', 'max:255'],
        'sort_order'  => ['nullable', 'integer', 'min:0'],
        'logo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
    ]);

    // لو فيه لوجو جديد
    if ($request->hasFile('logo')) {
        // حذف القديم إن وُجد
        if ($partner->logo_path && file_exists(public_path('storage/' . $partner->logo_path))) {
            unlink(public_path('storage/' . $partner->logo_path));
        }

        $partner->logo_path = $request->file('logo')->store('partners', 'public');
    }

    // تحديث باقي البيانات
    $partner->update([
        'name'        => $validated['name'],
        'website_url' => $validated['website_url'] ?? null,
        'sort_order'  => $validated['sort_order'] ?? 0,
    ]);

    $partner->logo_url = $partner->logo_path
        ? asset('storage/' . $partner->logo_path)
        : null;

    return response()->json([
        'success' => true,
        'message' => 'Partner updated successfully.',
        'data'    => $partner,
        'errors'  => null,
    ]);
}

}
