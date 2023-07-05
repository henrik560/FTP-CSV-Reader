<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateDebtorRequest;
use App\Models\Debtor;
use App\Services\AuthenticationService;
use App\Services\DebtorService;
use App\Services\PasswordService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    public function authenticate(AuthenticateDebtorRequest $request, AuthenticationService $authenticationService)
    {
        $validated = $request->validated();

        return $authenticationService->authenticate($request);
    }

    public function requestResetPassword(Request $request, PasswordService $passwordService)
    {
        $validated = $request->validate([
            'email' => 'required'
        ]);

        $token = $passwordService->generateToken($request->get('email'));

        return response()->json($token);
    }

    public function resetPassword(Request $request, PasswordService $passwordService, DebtorService $debtorService)
    {
        $validated = $request->validate([
            'password' => 'required',
            'token' => 'required'
        ]);

        if (!$debtor = $passwordService->verifyTokenExpiry($request->get('token'))) {
            throw new HttpResponseException(response()->json(["error" => "Request token expired or invalid!"]));
        };

        $debtorService->updatePassword($request->get('password'), $debtor);

        return response()->json(["message" => "Succesvol wachtwoord gewijzigd!"]);
    }
}
