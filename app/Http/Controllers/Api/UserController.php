<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
 use Illuminate\Support\Facades\Storage; // فوق مع الـ use

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
  $data = $request->validate([
    'name'           => 'required|string|max:100',
    'email'          => 'required|email|max:100|unique:users,email',
    'password'       => 'required|string|min:6',
    'phone'          => 'nullable|string|max:20',
    'role'           => 'required|in:doctor,parent,admin',
    'gender'         => 'nullable|in:male,female',
    'date_of_birth'  => 'nullable|date',
    'profile_image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
]);


    $data['password'] = Hash::make($data['password']);

    // رفع الصورة وتخزين الـ path
    if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')->store('users', 'public');
        $data['profile_image'] = $path;
    }

    $user = User::create($data);

    return response()->json([
        'message' => 'User created successfully',
        'data'    => $user
    ], 201);
}


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */

public function update(Request $request, User $user)
{
$data = $request->validate([
    'name'           => 'sometimes|string|max:100',
    'email'          => 'sometimes|email|max:100|unique:users,email,' . $user->user_id . ',user_id',
    'phone'          => 'nullable|string|max:20',
    'password'       => 'sometimes|string|min:6',
    'role'           => 'sometimes|in:doctor,parent,admin',
    'gender'         => 'nullable|in:male,female',
    'date_of_birth'  => 'nullable|date',
    'profile_image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
]);



    if (isset($data['password'])) {
        $data['password'] = Hash::make($data['password']);
    }

    // لو جت صورة جديدة
    if ($request->hasFile('profile_image')) {
        // احذفي القديمة لو موجودة
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $path = $request->file('profile_image')->store('users', 'public');
        $data['profile_image'] = $path;
    }

    $user->update($data);

    return response()->json([
        'message' => 'User updated successfully',
        'data' => $user
    ]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
