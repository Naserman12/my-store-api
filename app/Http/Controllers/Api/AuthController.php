<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /* ================= REGISTER ================= */

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;
        $sessionId = request()->header('X-Session-ID');

        Cart::where('session_id', $sessionId)
        ->update(['user_id' => $user->id, 'session_id' => null]);

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    /* ================= LOGIN ================= */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials']
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
            $sessionId = request()->header('X-Session-ID');
            Cart::where('session_id', $sessionId)
        ->update(['user_id' => $user->id, 'session_id' => null]);
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    /* ================= LOGOUT ================= */

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }

    /* ================= USER ================= */

    public function user(Request $request)
    {
        return $request->user();
    }
}