<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller {

    public function create(Request $request) {

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'description' => 'string',
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
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json($category, JsonResponse::HTTP_OK);

        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function edit(Request $request, $id) {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'description' => 'string',
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

            $category = Category::find($id);
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json($category, JsonResponse::HTTP_OK);

        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}