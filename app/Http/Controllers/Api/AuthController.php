<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|regex:/^[0-9]{10}$/',
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'Vui lòng cung cấp số điện thoại.',
            'username.regex' => 'Số điện thoại phải có 10 chữ số.',
            'password.required' => 'Vui lòng cung cấp mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Đăng nhập thành công',
                'data' => $user->loadCount('orders')->toResource(),
            ], 200);
        }

        return response()->json(['message' => 'Số điện thoại hoặc mật khẩu không đúng'], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|regex:/^[0-9]{10}$/',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng cung cấp tên của bạn.',
            'username.required' => 'Vui lòng cung cấp số điện thoại.',
            'username.regex' => 'Số điện thoại phải có 10 chữ số.',
            'username.unique' => 'Số điện thoại đã được sử dụng.',
            'password.required' => 'Vui lòng cung cấp mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $user = User::create([
            'name' => Str::ucfirst($request->name),
            'username' => $request->username,
            'password' => $request->password,
            'is_admin' => false,
            'address' => null,
        ]);

        // Chuyển đổi Guest user sang User
        Order::where('guest_phone', $user->username)->update(['user_id' => $user->id]);

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Đăng ký thành công',
            'data' => $user->loadCount('orders')->toResource(),
        ], 201);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Đăng xuất thành công'], 200);
    }
}
