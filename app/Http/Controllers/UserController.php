<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {
    public function editProfile(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|min:2|max:18',
                'middle_name' => 'string|min:2|max:18|nullable',
                'second_name' => 'required|min:2|max:18',
                'phone' => 'required|min:9|max:18',
                'gender' => 'required|in:male,female',
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
            Auth::user()->profile->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'second_name' => $request->second_name,
                'phone' => $request->phone,
                'gender' => $request->gender,
            ]);

            return response()->json(Auth::user()->profile, JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}