<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'user_id' => $this->user_id,
            'guest_name' => $this->guest_name,
            'guest_phone' => $this->guest_phone,
            'total_price' => $this->total_price,
            'address' => $this->address,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'dishes' => $this->dishes->map(function ($dish) {
                $image_url = null;
                if (Storage::disk('public')->exists($dish->image)) {
                    $image_url = Storage::disk('public')->url($dish->image);
                }
                return [
                    'id' => $dish->id,
                    'name' => $dish->name,
                    'image' => $image_url,
                    'meal_number' => $dish->pivot->meal_number,
                ];
            })->toArray(),
        ];
    }
}
