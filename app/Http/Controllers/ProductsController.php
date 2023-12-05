<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Subscription;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller {
    public function getAll() {
        try {
            $products = Product::all();

            return response()->json($products, JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function getById($id) {
        try {
            $product = Product::find($id);

            return response()->json($product, JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function create(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'price' => 'required',
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
            $product = Product::create([
                'name' => $request->name,
                'price' => $request->price,
            ]);

            return response()->json($product, JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function edit(Request $request, $id) {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'price' => 'required',
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
            $product = Product::find($id);

            $product->update([
                'name' => $request->name,
                'price' => $request->price,
            ]);

            return response()->json($product, JsonResponse::HTTP_OK);
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
                    'is_favourite' => true,
                ]);

                return response()->json(['message' => 'Продукт успешно добавлен в избранное'], JsonResponse::HTTP_OK);
            }

            $subscription = new Subscription([
                'product_id' => $product->id,
                'is_favourite' => true,
            ]);
            Auth::user()->subscriptions()->save($subscription);

            return response()->json(['message' => 'Продукт успешно добавлен в избранное'], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}