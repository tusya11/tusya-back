<?php

namespace App\Http\Controllers;

use App\Constants\RoleConstants;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {
    public function registration(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:4|max:18',
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
            $profile = new Profile([
                'first_name' => null,
                'middle_name' => null,
                'second_name' => null,
                'phone' => null,
                'gender' => null,
            ]);

            $user = User::create([
                'email' => strtolower($request->email),
                'password' => bcrypt($request->password),
                'role_id' => RoleConstants::CUSTOMER_ROLE,
            ]);

            $user->profile()->save($profile);
            $user->update([
                'profile_id' => $profile->id,
            ]);

            Auth::attempt($request->only('email', 'password'));
            $token = Auth::user()->createToken(config('app.name'));
            $token->token->expires_at = Carbon::now()->addDay();
            $token->token->save();

            return response()->json(['user' => new UserResource($user), 'token' => $token->accessToken], JsonResponse::HTTP_OK);

        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function login(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|min:4|max:18',

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
            $token = null;

            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Неверный логин или пароль!',
                ], JsonResponse::HTTP_UNAUTHORIZED);
            } else {
                $token = Auth::user()->createToken(config('app.name'));
                $token->token->expires_at = Carbon::now()->addDay();
                $token->token->save();
            }

            return response()->json(['user' => new UserResource(Auth::user()), 'token' => $token->accessToken], JsonResponse::HTTP_OK);

        } catch (Exception $exception) {
            return response()->json(['techError' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}