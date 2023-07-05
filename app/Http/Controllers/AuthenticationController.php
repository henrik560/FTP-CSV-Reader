<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateDebtorRequest;
use App\Http\Requests\CreateDebtorRequest;
use App\Models\Debtor;
use App\Services\AuthenticationService;
use App\Services\DebtorService;
use App\Services\PasswordService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function resetPassword(Request $request, string $token, PasswordService $passwordService, DebtorService $debtorService)
    {
        $validated = $request->validate([
            'password' => 'required',
        ]);

        if (!$debtor = $passwordService->verifyTokenExpiry($token)) {
            return response()->json(["error" => "Request token expired or invalid!"]);
        };

        $debtorService->updatePassword($request->get('password'), $debtor);

        return response()->json(["message" => "Succesvol wachtwoord gewijzigd!"]);
    }

    public function register(CreateDebtorRequest $request)
    {
        $validated = $request->validated();

        if (Debtor::whereEmail($request->get('email'))->first() !== null) {
            return response()->json(["error" => "there already exists a user with this e-mailadress"], Response::HTTP_BAD_REQUEST);
        }

        Debtor::create([$request->toArray()]);
    }
}
