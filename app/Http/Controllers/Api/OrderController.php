<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SergiX44\Nutgram\Nutgram;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Nutgram $bot)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
            'dishes' => 'required|array|min:1',
            'dishes.*' => 'required|array|min:1',
            'dishes.*.*' => 'required|exists:dishes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'order_no' => $this->genOrderNumber(),
                'user_id' => $request->user_id,
                'address' => $request->address,
                'notes' => $request->notes,
                'status' => 'pending',
                'total_price' => 35000 * count($request->dishes)
            ]);

            $pivotData = [];

            foreach ($request->dishes as $mealIndex => $dishIds) {
                $mealNumber = $mealIndex + 1;

                foreach ($dishIds as $dishId) {
                    $pivotData[$dishId] = ['meal_number' => $mealNumber];
                }
            }

            $order->dishes()->sync($pivotData);

            DB::commit();

            return response()->json([
                'message' => 'Tạo đơn hàng thành công',
                'order' => $order->load('dishes')
            ], 201);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => 'Lỗi khi đặt hàng: '.$th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::guard('web')->id())->first();

        if (!$order) {
            return response()->json([
                'message' => 'Không tìm thấy đơn hàng hoặc bạn không có quyền truy cập!',
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin đơn hàng thành công!',
            'order' => $order->load('dishes'),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    private function genOrderNumber()
    {
        do {
            $random = Str::random(8);
            $orderNo = 'XAN_' . $random;
        } while (Order::where('order_no', $orderNo)->exists());

        return $orderNo;
    }
}
