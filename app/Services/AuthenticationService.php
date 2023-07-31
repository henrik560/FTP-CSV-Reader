<?php

namespace App\Services;

use App\Http\Requests\AuthenticateDebtorRequest;
use App\Models\Debtor;
use App\Notifications\CreatePasswordMail;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    public function authenticate(AuthenticateDebtorRequest $request, PasswordService $passwordService)
    {
        if (!$debtor = Debtor::where('email', $request->get('user'))->first()) {
            return response()->json(['error' => 'Invalid username or password!'], Response::HTTP_BAD_REQUEST);
        }

        if ($debtor && is_null($debtor->password)) {
            $token = $passwordService->generateToken($debtor);
            $debtor->notify(new CreatePasswordMail($token, $debtor));
            return response()->json(["error" => "This user does not have a password, please follow the instructions sent to your mail"]);
        }

        if (!$this->validatePassword($debtor, $request->get('password'))) {
            return response()->json(["error" => "Username or password is incorrect!",], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            "authenticated" => true,
            "debtor" => $debtor,
        ], Response::HTTP_ACCEPTED);
    }

    private function validatePassword(Debtor $debtor, string $password): bool
    {
        return Hash::check($password, $debtor->password);
    }
}
