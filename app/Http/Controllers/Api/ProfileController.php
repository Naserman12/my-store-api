<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Get profile
    public function show(Request $request)
{
    return response()->json($request->user());
}
// Update profile
public function update(Request $request)
{
    $user = $request->user();

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:20',
    ]);

    $user->update($data);

    return response()->json([
        'message' => 'Profile updated',
        'user' => $user
    ]);
}

// Update avatar
public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|max:2048'
    ]);

    $path = $request->file('avatar')->store('avatars','public');

    $user = $request->user();
    $user->update([
        'avatar' => $path
    ]);

    return response()->json([
        'avatar' => $path
    ]);
}
// Update password
public function updatePassword(Request $request)
{
    $user = $request->user();
    $data = $request->validate([
        'current_password' => 'required',
        'password' => 'required|min:6|confirmed'
    ]);

    if (!Hash::check($data['current_password'], $user->password)) {
        return response()->json([
            'message' => 'Wrong password'
        ], 422);
    }
    $user->update([
        'password' => Hash::make($data['password'])
    ]);
    return response()->json([
        'message' => 'Password updated'
    ]);
}
}
