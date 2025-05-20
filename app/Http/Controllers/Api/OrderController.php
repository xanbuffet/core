<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use App\Models\Order;
use App\Traits\TelegramHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class OrderController extends Controller
{
    use TelegramHelperTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Nutgram $bot)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'note' => 'nullable|string',
            'dishes' => 'required|array',
            'dishes.*' => 'array',
            'dishes.*.*' => 'integer|exists:dishes,id',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'note' => $validated['note'] ?? '',
                'status' => 'pending',
                'total' => 35 * count($validated['dishes']),
            ]);
            $allDishIds = collect($validated['dishes'])->flatten()->unique()->values()->all();
            $order->dishes()->sync($allDishIds);

            DB::commit();

            $menu = [];
            $message = [];
            $message[] = '*[ĐƠN HÀNG MỚI]*';
            $message[] = '*Tên:* `'.$this->escapeMarkdownV2($order->name).'`';
            $message[] = '*SĐT:* `'.$this->escapeMarkdownV2($order->phone).'`';
            $message[] = '*Địa chỉ:* `'.$this->escapeMarkdownV2($order->address).'`';
            $message[] = '*Ghi chú:* `'.$this->escapeMarkdownV2($order->note).'`';
            $message[] = '*Trạng thái:* `'.$this->escapeMarkdownV2($order->status).'`';
            foreach ($validated['dishes'] as $index => $id) {
                $name = Dish::whereIn('id', $id)->pluck('name')->toArray();
                $formattedDishes = implode(', ', $name);
                $message[] = '*Suất '.($index + 1).':* '.$formattedDishes;
                $menu[] = 'Suất '.($index + 1).': '.$formattedDishes;
            }
            $bot->sendMessage(implode("\n", $message), env('TELEGRAM_CHAT_ID'), null, ParseMode::MARKDOWN);

            $order->menu = implode("\n", $menu);
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'orderId' => $order->id,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['error' => 'Lỗi khi đặt hàng: '.$th->getMessage()], 500);
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
