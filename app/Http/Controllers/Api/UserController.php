<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Chưa xác thực.'], 401);
        }

        return $user->loadCount('orders')->toResource();
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'regex:/^[0-9]{10}$/',
                Rule::unique('users')->ignore($user->id),
            ],
            'address' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Tên là bắt buộc.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'username.required' => 'Số điện thoại là bắt buộc.',
            'username.regex' => 'Số điện thoại phải có đúng 10 chữ số.',
            'username.unique' => 'Số điện thoại đã được sử dụng.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if (Auth::id() !== $user->getAuthIdentifier()) {
            return response()->json([
                'message' => 'Bạn không có quyền cập nhật thông tin người dùng này.',
            ], 403);
        }

        try {
            $user->update([
                'name' => $request->name,
                'username' => $request->username,
                'address' => $request->address,
            ]);

            return response()->json([
                'message' => 'Cập nhật thông tin người dùng thành công!',
                'data' => $user->loadCount('orders')->toResource(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể cập nhật thông tin người dùng.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the user password.
     */
    public function updatePassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Mật khẩu hiện tại là bắt buộc.',
            'new_password.required' => 'Mật khẩu mới là bắt buộc.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if (Auth::id() !== $user->getAuthIdentifier()) {
            return response()->json([
                'message' => 'Bạn không có quyền cập nhật mật khẩu của người dùng này.',
            ], 403);
        }

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không đúng.',
            ], 422);
        }

        try {
            $user->update([
                'password' => $request->new_password,
            ]);

            return response()->json([
                'message' => 'Cập nhật mật khẩu thành công!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Xoá người dùng thành công!',
        ], 200);
    }
}
