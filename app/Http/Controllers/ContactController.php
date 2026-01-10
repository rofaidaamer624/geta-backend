<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;


class ContactController extends Controller
{
 public function send(Request $request)
{
    $data = $request->validate([
        'first_name' => 'required|string',
        'last_name'  => 'required|string',
        'user_email' => 'required|email',
        'subject'    => 'required|string',
        'message'    => 'required|string',
    ]);

Mail::to(env('CONTACT_RECEIVER_EMAIL'))->send(new ContactMail($data));

    return response()->json(['message' => 'Message Sent Successfully'], 200);
}

}
