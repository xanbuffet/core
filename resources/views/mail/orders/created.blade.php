<x-mail::message>
# ĐƠN HÀNG MỚI ĐƯỢC TẠO

Chúng ta có một đơn hàng mới, đây là thông tin chi tiết:

Tên: **{{ $order->guest_name ?? $order->user->name }}**<br>
SĐT: **{{ $order->guest_phone ?? $order->user->username }}**<br>
Địa chỉ: **{{ $order->address }}**<br>
Trạng thái: **{{ $order->status }}**<br>

# MÓN ĂN
@php
    $meals = $order->dishes->groupBy('pivot.meal_number');
@endphp

@foreach ($meals as $mealNumber => $dishes)
### Suất {{ $mealNumber }}
@foreach ($dishes as $dish)
- {{ $dish->name }}
@endforeach
@endforeach

<x-mail::button url="">
XEM NGAY
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
