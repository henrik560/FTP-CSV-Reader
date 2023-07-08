<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateRequestService
{
    public function validateRequest(array $request, array $rules)
    {
        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $response = new JsonResponse([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);

            throw new HttpResponseException($response);
        }
    }
}
