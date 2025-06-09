<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Order extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isGuestOrder()
    {
        return is_null($this->user_id);
    }

    public function dishes()
    {
        return $this->belongsToMany(Dish::class)
            ->withPivot('meal_number')
            ->withTimestamps();
    }

    public function drinks()
    {
        return $this->belongsToMany(Drink::class)
            ->withPivot('quantity', 'total_price')
            ->withTimestamps();
    }

    public function orderDishes()
    {
        return $this->hasMany(OrderDish::class);
    }

    public function getCustomerInfo()
    {
        if ($this->isGuestOrder()) {
            return [
                'name' => $this->guest_name,
                'phone' => $this->guest_phone,
                'address' => $this->address,
            ];
        }

        return [
            'name' => $this->user->name,
            'phone' => $this->user->username,
            'address' => $this->user->address,
        ];
    }

    public function getMealCountAttribute()
    {
        return $this->dishes()->max('meal_number') ?? 0;
    }

    public function calculateTotalPrice()
    {
        $pricePerMeal = env('PRICE_PER_MEAL', 35000.00);
        $this->total_price = $this->meal_count * $pricePerMeal;
        $this->save();
    }

    protected static function booted(): void
    {
        static::updated(function (Order $order) {
            Cache::forget('xan.api.order.'.$order->id);
        });
    }
}
