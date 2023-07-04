<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateDebtorRequest;
use App\Services\AuthenticationService;

class AuthenticationController extends Controller
{
    public function authenticate(AuthenticateDebtorRequest $request, AuthenticationService $authenticationService)
    {
        $validated = $request->validated();

        return $authenticationService->authenticate($request);
    }

    public function resetPassword()
    {
    }

    public function requestResetPassword(Request $request)
    {
        // TODO Validate request with valid email 
        // generate a 24 hour reset key
    }
}
