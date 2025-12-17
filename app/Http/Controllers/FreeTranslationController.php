<?php

namespace App\Http\Controllers;

use App\Models\FreeTranslationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class FreeTranslationController extends Controller
{
    /** ========== Public: user submits a request (website form) ========== */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255'],
            'source_language'  => ['required', 'string', 'max:255'],
            'target_language'  => ['required', 'string', 'max:255'],
            'notes'            => ['nullable', 'string'],
            'file'             => ['required', 'file', 'mimes:pdf,doc,docx,txt,rtf,png,jpg,jpeg', 'max:5120'],
        ]);

        // رفع الملف إلى storage/app/public/free-requests
        $filePath = $request->file('file')->store('free-requests', 'public');

        $req = FreeTranslationRequest::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'source_language' => $validated['source_language'],
            'target_language' => $validated['target_language'],
            'notes'           => $validated['notes'] ?? null,
            'file_path'       => $filePath,
            'status'          => 'new',
        ]);

        // إضافة رابط مباشر للملف
        $req->file_url = $req->file_path ? asset('storage/' . $req->file_path) : null;

        // إرسال إيميل إشعار (اختياري، ما يوقفش الـ API لو حصل خطأ)
        try {
            Mail::raw(
                "New Free Translation Request from {$req->name} ({$req->email})",
                function ($msg) {
                    $msg->to('info@transgate.com')->subject('New Free Translation Request');
                }
            );
        } catch (\Throwable $e) {
            // تجاهل خطأ الإيميل
        }

        return response()->json([
            'success' => true,
            'message' => 'Request submitted successfully.',
            'data'    => $req,
            'errors'  => null,
        ], 201);
    }

    /** ========== Admin: list all requests ========== */
    public function index()
    {
        $requests = FreeTranslationRequest::orderBy('created_at', 'desc')->get();

        foreach ($requests as $r) {
            $r->file_url = $r->file_path ? asset('storage/' . $r->file_path) : null;
        }

        return response()->json([
            'success' => true,
            'message' => 'Requests fetched successfully.',
            'data'    => ['requests' => $requests],
            'errors'  => null,
        ]);
    }

    /** ========== Admin: show single request ========== */
    public function show($id)
    {
        $req = FreeTranslationRequest::find($id);

        if (! $req) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $req->file_url = $req->file_path ? asset('storage/' . $req->file_path) : null;

        return response()->json([
            'success' => true,
            'message' => 'Request fetched successfully.',
            'data'    => $req,
            'errors'  => null,
        ]);
    }

    /** ========== Admin: update status only ========== */
    public function updateStatus(Request $request, $id)
    {
        $req = FreeTranslationRequest::find($id);

        if (! $req) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:new,in_progress,done'],
        ]);

        $req->status = $validated['status'];
        $req->save();

        $req->file_url = $req->file_path ? asset('storage/' . $req->file_path) : null;

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'data'    => $req,
            'errors'  => null,
        ]);
    }

    /** ========== Admin: delete request ========== */
    public function destroy($id)
    {
        $req = FreeTranslationRequest::find($id);

        if (! $req) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        if ($req->file_path && Storage::disk('public')->exists($req->file_path)) {
            Storage::disk('public')->delete($req->file_path);
        }

        $req->delete();

        return response()->json([
            'success' => true,
            'message' => 'Request deleted successfully.',
            'data'    => null,
            'errors'  => null,
        ]);
    }
}
