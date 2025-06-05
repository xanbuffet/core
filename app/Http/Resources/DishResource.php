<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DishResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image_url = null;
        if (Storage::disk('public')->exists($this->image)) {
            $image_url = Storage::disk('public')->url($this->image);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $image_url,
        ];
    }
}
