<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /* ================= REGISTER ================= */

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|regex:/[a-zA-Z]/|regex:/[0-9]/|regex:/^[a-zA-Z0-9!@#$^&*()_\+\-=\[\]{};:\'",.<>\/?]*$/',
        ]);
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;
       

        $sessionId = $request->header('X-Session-ID');

        $this->mergeCarts($user->id, $sessionId);

        $cart = Cart::with('items.product')
        ->where('user_id', $user->id)
        ->first();
       if ($user) {
         sendNotification(
            $user->id,
            "🎉 أهلاً بك",
            "مرحباً {$user->name} في متجرنا ❤️ هذا التنبه بانك فتحت حساب جديد نتمنى لك التوفيق"
        );
        }
    return response()->json([
        'user' => $user,
        'token' => $token,
        'cart' => $cart
    ]);
    }

    /* ================= LOGIN ================= */

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials  = $request->only('email', 'password');

        if (!Auth::attempt($credentials )) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials']
            ]);
        }
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;          
        $sessionId = $request->header('X-Session-ID');
        $this->mergeCarts($user->id, $sessionId);
        $cart = Cart::with('items.product')
        ->where('user_id', $user->id)
        ->first();
        if ($user) {
            sendNotification(
                $user->id,
                "🎉 أهلاً بك",
                "مرحباً {$user->name} في متجرنا ❤️ هذا التنبه بانك سجلت دخول شكرا لوقت"
                );
        }
        return response()->json([
            'user' => $user,
            'token' => $token,
            'cart' => $cart, 
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
    private function mergeCarts($userId, $sessionId)
    {
        if (!$sessionId) {
            $sessionId = request()->header('X-Session-ID');
        }

        if (!$sessionId || $sessionId === "null") return;

        $guestCart = Cart::with('items')
            ->where('session_id', $sessionId)
            ->first();

        $userCart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['session_id' => null]
        );

        if (!$guestCart) return;

        // 🔥 مهم جداً
        if ($guestCart->id === $userCart->id) return;

        foreach ($guestCart->items as $item) {

            $existing = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $item->product_id)
                ->first();

            if ($existing) {
                $existing->quantity += $item->quantity;
                $existing->save();
            } else {
                CartItem::create([
                    'cart_id' => $userCart->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity
                ]);
            }
        }
         // 🔥 اربط session_id بسلة المستخدم
        $userCart->session_id = $sessionId;
        $userCart->save();
        $guestCart->items()->delete();
        $guestCart->delete();
    }
    public function user(Request $request)
    {
        return $request->user();
    }
}