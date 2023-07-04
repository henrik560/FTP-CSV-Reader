<?php

namespace App\Services;

use App\Http\Requests\AuthenticateDebtorRequest;
use App\Models\Debtor;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    private $debtorService;

    public function __construct(DebtorService $debtorService)
    {
        $this->debtorService = $debtorService;
    }

    public function authenticate(AuthenticateDebtorRequest $request)
    {
        if (!$debtor = Debtor::where('email', $request->get('user'))->first()) {
            $this->throwUnAuthenticatedError();
        }

        if (!isset($debtor['password'])) {
            $this->debtorService->generatePassword($debtor);

            // TODO create a return with response
        }

        if (!$this->validatePassword($debtor, $request->get('password'))) {
            return response()->json([
                "error" => "Username or password is incorrect!",
            ]);
        }

        return response()->json([
            "authenticated" => true,
            "debtor" => $debtor,
        ]);
    }

    private function validatePassword(Debtor $debtor, string $password): bool
    {
        return Hash::check($password, $debtor->password);
    }

    private function throwUnAuthenticatedError(): HttpResponseException
    {
        return new HttpResponseException(response()->json(['error' => 'Invalid username or password!'], Response::HTTP_BAD_REQUEST));
    }
}
