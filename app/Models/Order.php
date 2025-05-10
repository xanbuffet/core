<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
