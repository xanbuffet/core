<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SergiX44\Nutgram\Nutgram;

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
            'address' => 'nullable|string|max:255',
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
                'user_id' => $request->user_id,
                'address' => $request->address,
                'notes' => $request->note,
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
                'message' => 'Order created successfully',
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
        $order = Cache::remember("xan.api.order.{$id}", now()->addMinutes(10), function () use ($id) {
            $order = Order::find($id);

            if ($order) {
                $order->load('dishes');
            }

            return $order;
        });

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
