<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\TelegramHelperTrait;
use Illuminate\Http\Request;
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
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'dishes' => 'array',
        ]);
        $order = Order::create($request->merge(['total' => 35])->only(['name', 'phone', 'address', 'note', 'total']));
        $order->status = 'pending';
        $order->save();
        $order->dishes()->sync($request->dishes);

        $dishNames = $order->dishes->pluck('name')->toArray();
        $message = "*\\[Đơn hàng mới\\]*\n"
            .'*Tên:* '.$this->escapeMarkdownV2($order->name)."\n"
            .'*SĐT:* '.$this->escapeMarkdownV2($order->phone)."\n"
            .'*Địa chỉ:* '.$this->escapeMarkdownV2($order->address)."\n"
            .'*Ghi chú:* '.($order->note ? $this->escapeMarkdownV2($order->note) : '_Không có_')."\n"
            ."*Món đã đặt:*\n"
            .implode("\n", array_map(fn ($i, $name) => ($i + 1).'\\. '.$name, array_keys($dishNames), $dishNames))."\n"
            .'*Trạng thái:* `'.$this->escapeMarkdownV2($order->status).'`';

        $bot->sendMessage(
            text: $message,
            chat_id: env('TELEGRAM_CHAT_ID'),
            parse_mode: ParseMode::MARKDOWN,
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Đơn hàng đã được gửi thành công',
            'orderId' => $order->id,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return 'ok';
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
