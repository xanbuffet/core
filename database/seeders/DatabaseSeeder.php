<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Dish;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(9)->create();
        User::factory()->admin()->create([
            'name' => 'Xan Admin',
            'username' => '0927733111',
        ]);

        $dishes = Dish::factory()->count(86)->create();
        $menus = Menu::factory(7)->create();

        $menus->each(function ($menu) use ($dishes) {
            $menu->dishes()->attach($dishes->random(16));
        });
    }
}
