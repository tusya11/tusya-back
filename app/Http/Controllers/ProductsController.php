<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\SaveFileTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    use SaveFileTrait;

    public function getAll()
    {
        try {
            $products = Product::all();

            return response()->json(ProductResource::collection($products), JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function getById($id)
    {
        try {
            $product = Product::find($id);

            return response()->json(new ProductResource($product), JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'category_id' => 'required',
                'name' => 'required',
                'description' => 'required',
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
                'category_id' => $request->category_id,
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'image' => $request->image ? SaveFileTrait::saveFile($request->image) : null,
            ]);

            return response()->json($product, JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function edit(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'description' => 'required',
                'price' => 'required',
                'image' => 'required',
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
                'description' => $request->description,
                'image' => $request->image ? SaveFileTrait::saveFile($request->image) : null,
            ]);

            return response()->json($product, JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function delete($id)
    {
        $product = Product::find($id);

        $product->delete();
    }

}
