<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        return new UserResource($user);
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
            'username' => ['required', 'string', 'regex:/^[0-9]{10}$/', 'unique:users,username,'.$user->username],
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

        if (Auth::id() !== $user->id) {
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
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể cập nhật thông tin người dùng.',
                'error' => $e->getMessage(),
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
