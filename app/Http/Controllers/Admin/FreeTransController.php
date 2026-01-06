<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FreeTranslationController extends Controller
{
    public function store(Request $request)
    {
        // ✅ Force JSON responses (avoid redirects)
        $request->headers->set('Accept', 'application/json');

        // ✅ Validate
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255',
            'mobile' => 'required|string|max:30',
            'file'   => 'required|file|max:5120', // 5MB
        ]);

        // ✅ Upload file to storage/app/public/free_translations
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('free_translations', 'public');
        }

        // ✅ Build email data
        $mailData = [
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'mobile' => $validated['mobile'],
            'filePath' => $filePath ? Storage::disk('public')->path($filePath) : null,
        ];

        try {
            // ✅ Send email
            Mail::send([], [], function ($message) use ($mailData, $filePath) {
                $message->to('info@transgateacd.com')
                    ->subject('New Free Translation Request')
                    ->setBody(
                        "New free translation request received:\n\n" .
                        "Name: {$mailData['name']}\n" .
                        "Email: {$mailData['email']}\n" .
                        "Mobile: {$mailData['mobile']}\n",
                        'text/plain'
                    );

                if ($filePath) {
                    $message->attach(Storage::disk('public')->path($filePath));
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Request sent successfully!',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mail failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
