<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    use HasFactory;

    public function dishes()
    {
        return $this->belongsToMany(Dish::class);
    }

    protected static function booted(): void
    {
        static::updated(function (Menu $menu) {
            Cache::forget('xan.api.menu.' . $menu->day_of_week);
        });
    }
}
