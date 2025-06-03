<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Xác thực dữ liệu đầu vào
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
            return response()->json([
                'statusCode' => 400,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Kiểm tra thông tin đăng nhập
        $credentials = [
            'username' => $request->username, // Giả sử cột trong DB là 'phone'
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Tạo token (nếu sử dụng Sanctum hoặc Passport)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'statusCode' => 200,
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'username' => $user->username,
                    'is_admin' => $user->is_admin,
                    'token' => $token,
                ],
            ], 200);
        }

        return response()->json([
            'statusCode' => 401,
            'message' => 'Số điện thoại hoặc mật khẩu không đúng',
        ], 401);
    }
}
