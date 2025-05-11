<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Order extends Model
{
    use HasFactory;

    public function dishes()
    {
        return $this->belongsToMany(Dish::class)->withTimestamps();
    }

    public function drinks()
    {
        return $this->belongsToMany(Drink::class)->withPivot('quantity', 'price')->withTimestamps();
    }

    public function orderDishes()
    {
        return $this->hasMany(OrderDish::class);
    }

    protected static function booted(): void
    {
        static::updated(function (Order $order) {
            Cache::forget('xan.api.order.' . $order->id);
        });
    }
}
