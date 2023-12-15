<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RatingProduct;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingProductController extends Controller {
    public function setRating(Request $request, $id) {
        $validator = Validator::make(
            $request->all(),
            [
                'rating' => 'required|numeric|between:1.0,5.0',
            ]
        );

        $errors = $validator->errors();

        if ($errors->all()) {
            return response()->json([
                'status' => 'error',
                'techError' => $errors->all(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Продукт не найден',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $ratingProduct = new RatingProduct([
                'product_id' => $product->id,
                'rating' => $request->rating,
            ]);

            $user = auth()->user();

            $existingRatingProductByUser = $user->ratings->where('product_id', $product->id)->first();

            if ($existingRatingProductByUser) {
                $existingRatingProductByUser->update([
                    'rating' => $request->rating,
                ]);
            } else {
                $user->ratings()->save($ratingProduct);
            }

            return response()->json(['message' => 'Success'], JsonResponse::HTTP_OK);

        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}