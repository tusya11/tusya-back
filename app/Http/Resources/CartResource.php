<?php

namespace App\Http\Resources;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            "id" => $this->id,
            "category_id" => $this->category_id,
            "category" => $this->category,
            "name" => $this->name,
            "price" => $this->price,
            "description" => $this->description,
            "image" => $this->image ? config('app.url') . $this->image : null,
            'is_favorite' => Subscription::where('user_id', auth()->user()->id)->where('product_id', $this->id)->first()->is_favourite,
        ];
    }
}
