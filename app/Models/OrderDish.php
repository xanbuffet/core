<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDish extends Model
{
    protected $table = 'dish_order';

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
