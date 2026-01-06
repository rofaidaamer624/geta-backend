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

            $fileFullPath = Storage::disk('public')->path($filePath);
            $fileUrl = url('/storage/' . $filePath);

            $body =
                "New free translation request received:\r\n\r\n" .
                "Name: {$mailData['name']}\r\n" .
                "Email: {$mailData['email']}\r\n" .
                "Mobile: {$mailData['mobile']}\r\n" .
                "File URL: {$fileUrl}\r\n";

            Mail::raw($body, function ($message) use ($fileFullPath) {
$message->to('info@transgateacd.com')
        ->cc('transgateac@outlook.com')
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                        ->subject('New Free Translation Request');

                if (file_exists($fileFullPath)) {
                    $message->attach($fileFullPath);
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
