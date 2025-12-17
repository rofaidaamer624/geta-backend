<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * عرض فورم تسجيل الدخول (صفحة الويب)
     * GET /admin/login
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * API: POST /api/admin/login
     */
    public function login(Request $request)
    {
        // 1) Validation
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // 2) البحث عن الأدمن
        $admin = AdminUser::where('email', $validated['email'])->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
                'data'    => null,
                'errors'  => [
                    'email' => ['The provided credentials are incorrect.'],
                ],
            ], 401);
        }

        // 3) توليد توكن جديد وتخزينه مشفّر
        $plainToken = Str::random(60);
        $admin->api_token = hash('sha256', $plainToken);
        $admin->save();

        // 4) الرد
        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully.',
            'data'    => [
                'user' => [
                    'id'    => $admin->id,
                    'name'  => $admin->name,
                    'email' => $admin->email,
                ],
                'token' => $plainToken,
            ],
            'errors' => null,
        ]);
    }
}
