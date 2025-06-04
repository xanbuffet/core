<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dish>
 */
class DishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dishes = [
            'Thịt Heo Sả Ớt',
            'Thịt Heo Kim Chi',
            'Thịt Heo Xào Mướp',
            'Thịt Heo Xào Hành Tây',
            'Thịt Heo Rang Tương',
            'Thịt Heo Xào Nấm',
            'Thịt Heo Rim Tương',
            'Thịt Heo Xào Cà Tím',
            'Thịt Heo Xào Chua Ngọt',
            'Thịt Heo Xào Măng',
            'Thịt Bò Sốt Vang',
            'Thịt Bò Cải Chíp',
            'Thịt Bò Xào Mướp Đắng',
            'Thịt Bò Xào Măng',
            'Thịt Bò Xào Cần Tỏi',
            'Thịt Bò Xào Ngô Bao Tử',
            'Thịt Bò Xào Hành Tây',
            'Thịt Bò Xào Dưa',
            'Thịt Bò Hành Tây Ớt Sừng',
            'Cà Ri Bò',
            'Thịt Bò Xào Ớt Chuông',
            'Xương Nấu Khoai Tây',
            'Sườn Rang',
            'Xương Nấu Khoai Môn',
            'Sườn Xào Chua Ngọt',
            'Xương Nấu Bí Đỏ',
            'Sườn Rim Dứa',
            'Cá Kho Riềng',
            'Cá Sốt Cà Chua',
            'Cá Mắm Chua Ngọt',
            'Cá Om Dưa',
            'Tim Xào Ớt Chuông Cần Tỏi',
            'Gan Cháy Tỏi',
            'Dạ Dày Xào Dưa',
            'Tim Cật Xào Cần Tỏi',
            'Tim Cật Xào Đỗ',
            'Dạ Dày Luộc',
            'Lòng Gà Xào Dứa',
            'Thịt Băm Rau Củ',
            'Lòng Gà Sả Ớt',
            'Mướp Đắng Nhồi Thịt',
            'Thịt Băm Đảo Khoai Tây',
            'Lòng Gà Xào Mướp',
            'Chả Mỡ Sốt Cà Chua',
            'Mọc Xúc Xích Chua Ngọt',
            'Chả Lá Lốt',
            'Nem',
            'Xúc Xích Bơ Bắp',
            'Chả Mỡ Rim Tiêu',
            'Mọc Xúc Xích Rim Tiêu',
            'Đậu Nấu Chuối',
            'Đậu Tẩm Hành',
            'Đậu Sốt Cà Chua',
            'Đậu Rim Mắm Sả Ớt',
            'Đậu Thịt Băm',
            'Đậu Rim',
            'Trứng Cuộn',
            'Trứng Rán Rau Củ',
            'Trứng Rim',
            'Trứng Đảo Hành',
            'Gà Rang Gừng',
            'Gà Hấp Lá Chanh',
            'Gà Rang Nghệ',
            'Cà Ri Gà',
            'Vịt Hấp Xì Dầu',
            'Vịt Sả Ớt',
            'Vịt Chiên Riềng',
            'Vịt Chua Ngọt',
            'Nộm Dưa Chuột',
            'Nộm Đu Đủ',
            'Salad Cải Bắp',
            'Cà Muối',
            'Nộm Hoa Chuối',
            'Kim Chi',
            'Su Su Xào',
            'Bí Đỏ Xào Tỏi',
            'Bầu Xào',
            'Rau Muống Xào Tỏi',
            'Su Su Luộc',
            'Cải Ngọt Xào',
            'Khoai Tây Sợi Xào',
            'Cải Bắp Luộc',
            'Rau Cần Xào',
            'Khoai Tây Sốt Cà Chua',
            'Bắp Cải Xào',
            'Cải Chíp Xào',
            'Cá Kho Dưa',
        ];

        return [
            'name' => fake()->unique()->randomElement($dishes),
            'image' => 'dishes/xan_dish404.webp',
        ];
    }
}
