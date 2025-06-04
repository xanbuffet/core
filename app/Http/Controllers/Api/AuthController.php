<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'name' => $user->name,
                    'username' => $user->username,
                    'is_admin' => $user->is_admin,
                    'token' => $token,
                    'address' => $user->address,
                ],
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
            'password.required' => 'Vui lòng cung cấp mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $user = User::create([
            'name' => Str::ucfirst($request->name),
            'username' => $request->username,
            'password' => $request->password,
            'is_admin' => false,
            'address' => null
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công',
            'data' => [
                'name' => $user->name,
                'username' => $user->username,
                'is_admin' => $user->is_admin,
                'token' => $token,
                'address' => $user->address
            ],
        ], 201);
    }
}
