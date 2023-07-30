<?php

namespace App\Http\Middleware;

use App\Models\Debtor;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ApiKeyAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $this->validateRequest($request);

        if (!$request->get("user")) {
            return response()->json([
                'error' => 'Please provide a user and a valid api key!',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$user = $this->getUser($request->get('user'))) {
            return response()->json([
                'error' => 'User does not exists',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->validateApiKey($user, $request->get('api-key'))) {
            return response()->json([
                'error' => 'Api key is invalid!',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }

    private function validateApiKey(Debtor $debtor, string $apiKey): bool
    {
        return $debtor['secret_key'] == $apiKey;
    }

    private function getUser(string $debtor): ?Debtor
    {
        return Debtor::where('email', $debtor)->first();
    }

    private function validateRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'user' => 'required',
                'api-key' => 'required',
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
