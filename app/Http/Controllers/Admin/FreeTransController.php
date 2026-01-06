<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
   use Illuminate\Support\Facades\Validator;

class FreeTranslationController extends Controller
{

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name'   => 'required|string|max:255',
        'email'  => 'required|email|max:255',
        'mobile' => 'required|string|max:30',
        'file'   => 'required|file|max:5120',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors'  => $validator->errors()
        ], 422);
    }

    $filePath = $request->file('file')->store('free_translations', 'public');

    $mailData = [
        'name'   => $request->name,
        'email'  => $request->email,
        'mobile' => $request->mobile,
    ];

    try {
        Mail::raw(
            "New free translation request received:\n\n" .
            "Name: {$mailData['name']}\n" .
            "Email: {$mailData['email']}\n" .
            "Mobile: {$mailData['mobile']}\n",
            function ($message) use ($filePath) {
                $message->to('info@transgateacd.com')
                        ->subject('New Free Translation Request')
                        ->attach(Storage::disk('public')->path($filePath));
            }
        );

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
