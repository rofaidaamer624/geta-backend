<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Public endpoint: user submits the contact form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Save to DB
        $contact = ContactMessage::create($validated);

        // Optional: send notification email
        try {
            Mail::raw("New contact message:\n\nFrom: {$validated['name']} ({$validated['email']})\n\nMessage:\n{$validated['message']}", function ($msg) {
                $msg->to('info@transgate.com')->subject('New Contact Message');
            });
        } catch (\Throwable $e) {
            // no crash if mail fails
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data'    => $contact,
            'errors'  => null,
        ], 201);
    }

    /**
     * Admin: list all messages
     */
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Contact messages fetched successfully.',
            'data'    => ['messages' => $messages],
            'errors'  => null,
        ]);
    }

    /**
     * Admin: view single message
     */
    public function show($id)
    {
        $message = ContactMessage::find($id);
        if (! $message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact message fetched successfully.',
            'data'    => $message,
            'errors'  => null,
        ]);
    }

    public function update(Request $request, $id)
{
    $message = ContactMessage::find($id);

    if (! $message) {
        return response()->json([
            'success' => false,
            'message' => 'Message not found.',
        ], 404);
    }

    $validated = $request->validate([
        'name'    => ['required', 'string', 'max:255'],
        'email'   => ['required', 'email', 'max:255'],
        'phone'   => ['nullable', 'string', 'max:50'],
        'subject' => ['nullable', 'string', 'max:255'],
        'message' => ['required', 'string', 'max:2000'],
    ]);

    $message->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Contact message updated successfully.',
        'data'    => $message,
        'errors'  => null,
    ]);
}

  
    public function destroy($id)
    {
        $message = ContactMessage::find($id);
        if (! $message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully.',
        ]);
    }


    public function updateReadStatus(Request $request, $id)
{
    $message = ContactMessage::find($id);

    if (! $message) {
        return response()->json([
            'success' => false,
            'message' => 'Message not found.',
            'data'    => null,
            'errors'  => null,
        ], 404);
    }

    $validated = $request->validate([
        'is_read' => ['required', 'boolean'],
    ]);

    $message->is_read = $validated['is_read'];
    $message->save();

    return response()->json([
        'success' => true,
        'message' => 'Read status updated successfully.',
        'data'    => $message,
        'errors'  => null,
    ]);
}



}
