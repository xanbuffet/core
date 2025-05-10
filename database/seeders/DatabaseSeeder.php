<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $dishes = \App\Models\Dish::factory(30)->create();
        $drinks = \App\Models\Drink::factory(20)->create();
        $menus = \App\Models\Menu::factory(7)->create();

        $menus->each(function ($menu) use ($dishes) {
            $menu->dishes()->attach($dishes->random(16));
        });

        // Tạo orders có dishes và drinks
        \App\Models\Order::factory(5)->create()->each(function ($order) use ($dishes, $drinks) {
            $order->dishes()->attach($dishes->random(6));

            $order->drinks()->attach($drinks->random(2), [
                'quantity' => rand(1, 2),
                'price' => fake()->randomFloat(2, 5, 50),
            ]);
        });
    }
}
