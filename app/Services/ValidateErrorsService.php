<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class ValidateErrorsService {
    public function validateErrors($request, $rules) {
        $validator = Validator::make(
            $request->all(),
            $rules
        );

        $errors = $validator->errors();

        if ($errors->all()) {
            return [
                'message' => [
                    'status' => 'error',
                    'techError' => $errors->all(),
                ],
                'status' => false,
            ];
        } else {
            return [
                'status' => true,
            ];
        }
    }
}
