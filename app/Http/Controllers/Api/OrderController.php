<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $orders = Order::with('dishes')->get();
        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Nutgram $bot)
    {
        $rules = [
            'address' => 'required|string|min:10|max:255',
            'notes' => 'nullable|string|max:255',
            'dishes' => 'required|array|min:1',
            'dishes.*' => 'required|array|min:1',
            'dishes.*.*' => 'required|exists:dishes,id',
        ];

        if ($request->has("type") && $request->type == "guest") {
            $rules = array_merge($rules, [
                'guest_name' => 'required|string|min:2',
                'guest_phone' => 'required|string|regex:/^[0-9]{10}$/',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'order_no' => $this->genOrderNumber(),
                'user_id' => $request->type == 'user' ? Auth::user()->id : null,
                'guest_name' => $request->guest_name,
                'guest_phone' => $request->guest_phone,
                'address' => $request->address,
                'notes' => $request->notes,
                'status' => 'pending',
                'total_price' => 35000 * count($request->dishes),
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
                'order' => $order->load('dishes')->toResource()
            ], 201);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => 'Lỗi khi đặt hàng: '.$th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        if ($order->user_id != Auth::id()) {
            return response()->json([
                'message' => 'Bạn không có quyền truy cập!',
            ], 403);
        }

        return response()->json([
            $order->load('dishes')->toResource(),
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
