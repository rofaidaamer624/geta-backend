<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * 🔹 تسجيل مستخدم جديد (Register)
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'full_name'     => 'required|string|max:100',
            'email'         => 'required|email|max:100|unique:users,email',
            'password'      => 'required|string|min:6',
            'phone'         => 'nullable|string|max:20',
            // لو ما بعتّيش role هتتسجل كـ parent تلقائيًا
            'role'          => 'nullable|in:doctor,parent,admin',
            'gender'        => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'profile_image' => 'nullable|string',
        ]);

        // نخلي الـ role الافتراضي parent لو مش مبعوت
        if (!isset($data['role'])) {
            $data['role'] = 'parent';
        }

        // تشفير الباسورد
        $data['password'] = Hash::make($data['password']);

        // إنشاء اليوزر
        $user = User::create($data);

        // إنشاء توكن على طول بعد التسجيل (اختياري بس حلو)
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    /**
     * 🔹 تسجيل الدخول
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials. Please check your email or password.',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'token'   => $token,
            'user'    => $user,
        ], 200);
    }

    /**
     * 🔹 بيانات المستخدم الحالي
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * 🔹 تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
