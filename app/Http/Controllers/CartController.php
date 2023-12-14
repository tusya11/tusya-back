<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\Subscription;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function getMyProducts()
    {
        try {
            $myProducts = [];

            foreach (Auth::user()->subscriptions as $subscription) {
                $myProducts[] = $subscription->product->load('category');
            }

            if (!count($myProducts)) {
                return response()->json(['message' => 'Корзина пуста'], JsonResponse::HTTP_OK);
            }

            return response()->json(CartResource::collection($myProducts), JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function getMyProductsFavorite()
    {
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

    public function addToCart(Request $request)
    {
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

    public function addToFavorite(Request $request)
    {
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

            $user = auth()->user();

            if ($request->is_favorite) {
                $favorite = new Favorite([
                    'product_id' => $product->id,
                ]);
                $user->favorites()->save($favorite);

                return response()->json(['message' => 'Продукт успешно добавлен в избранное'], JsonResponse::HTTP_OK);
            } elseif (!$request->is_favorite) {
                $user->favorites()->where('product_id', $product->id)->delete();

                return response()->json(['message' => 'Продукт успешно удален из избранного'], JsonResponse::HTTP_OK);
            }

        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
