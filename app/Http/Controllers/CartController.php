<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Product;
use App\Models\Subscription;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller {
    public function getMyProducts() {

        try {

            $myProducts = [];

            foreach (Auth::user()->subscriptions as $subscription) {
                $myProducts[] = $subscription->product->load('category');
            }

            return response()->json(CartResource::collection($myProducts), JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function getMyProductsFavorite() {
        try {

            $myProducts = [];

            foreach (Auth::user()->subscriptions->where('is_favourite') as $subscription) {
                $myProducts[] = $subscription->product->load('category');
            }

            return response()->json(CartResource::collection($myProducts), JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function addToCart(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'product_id' => 'required',
            ]
        );

        $errors = $validator->errors();

        if ($errors->all()) {
            return response()->json([
                'status' => 'error',
                'techError' => $errors->all(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $product = Product::find($request->product_id);

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Продукт не найден',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $subscription = new Subscription([
                'product_id' => $product->id,
                'is_favourite' => false,
            ]);
            Auth::user()->subscriptions()->save($subscription);

            return response()->json(['message' => 'Продукт успешно добавлен в корзину'], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function addToFavourite(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'product_id' => 'required',
                'is_favorite' => 'boolean',
            ]
        );

        $errors = $validator->errors();

        if ($errors->all()) {
            return response()->json([
                'status' => 'error',
                'techError' => $errors->all(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $product = Product::find($request->product_id);

            if (!$product) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Продукт не найден',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
            $existingSubscription = Subscription::where('user_id', Auth::user()->id)->where('product_id', $product->id)->first();
            if ($existingSubscription) {
                $existingSubscription->update([
                    'is_favourite' => $request->is_favorite,
                ]);

                return response()->json(['message' => 'Продукт успешно добавлен в избранное'], JsonResponse::HTTP_OK);
            }

            $subscription = new Subscription([
                'product_id' => $product->id,
                'is_favourite' => $request->is_favorite,
            ]);
            Auth::user()->subscriptions()->save($subscription);

            return response()->json(['message' => 'Продукт успешно добавлен в избранное'], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}