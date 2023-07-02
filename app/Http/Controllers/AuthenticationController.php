<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateUserRequest;
use App\Models\Debtor;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class AuthenticationController extends Controller
{
    public function index(AuthenticateUserRequest $request)
    {
        $validated = $request->validated();

        if (!$user = Debtor::where('email', $request->get('user')->first())) {
            $this->throwUnAuthenticatedError();
        }

        // TODO Check if debtor has password
        if (!isset($user["password"])) {
            // TODO notify user of new password 
        }

        //TODO check if passwords are equal
    }

    private function throwUnAuthenticatedError(): HttpResponseException
    {
        return new HttpResponseException(response()->json(["error" => "Invalid username or password!"], Response::HTTP_BAD_REQUEST));
    }
}
