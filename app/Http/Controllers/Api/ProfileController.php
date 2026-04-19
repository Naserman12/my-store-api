<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

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
    $user = $request->user();
    // إعداد Cloudinary
    Configuration::instance([
        'cloud' => [
            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            'api_key'    => env('CLOUDINARY_API_KEY'),
            'api_secret' => env('CLOUDINARY_API_SECRET'),
        ],
        'url' => [
            'secure' => true
        ]
    ]);
    // حذف القديم
    // if ($user->avatar_public_id) {
    //     (new UploadApi())->destroy($user->avatar_public_id);
    // }
    // رفع الصورة
    $result = (new UploadApi())->upload(
        $request->file('avatar')->getRealPath(),
        [
            'folder' => 'avatars',
            'transformation' => [
            'width' => 300,
            'height' => 300,
            'crop' => 'fill',
            'quality' => 'auto',
            'fetch_format' => 'auto'
        ]
        ]
    );
    $user->update([
        'avatar' => $result['secure_url'],
        // 'avatar_public_id' => $result['public_id'], // ✅ مهم
    ]);
    $user->refresh();
return response()->json([
    'avatar' => $user->avatar
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
