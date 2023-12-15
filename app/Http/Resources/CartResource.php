<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
            'rating' => self::calculateRating($this->ratings),
            "image" => $this->image ? config('app.url') . $this->image : null,
            'is_favorite' => self::checkOnIsFavorite($this->id),
        ];
    }

    private static function checkOnIsFavorite($productId) {
        if (Auth::guard('api')->check()) {
            if (Auth::guard('api')->user()->favorites->where('product_id', $productId)->count()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private static function calculateRating($ratings) {
        $resultRating = 0;
        $amount = $ratings->count();

        foreach ($ratings as $ratingProduct) {
            $resultRating += $ratingProduct->rating;
        }

        return $amount ? round(($resultRating / $amount), 1) : 0;
    }
}