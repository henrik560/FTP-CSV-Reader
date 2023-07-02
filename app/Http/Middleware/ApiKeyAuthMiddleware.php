<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ApiKeyAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $this->validateRequest($request);

        if (!$user = $this->getUser($request->get("user"))) {
            throw new HttpResponseException(response()->json([
                'error' => 'User does not exists'
            ], Response::HTTP_BAD_REQUEST));
        }

        if (!$this->validateApiKey($user, $request->get('api-key'))) {
            throw new HttpResponseException(response()->json([
                'error' => 'Api key is invalid!'
            ], Response::HTTP_BAD_REQUEST));
        }

        return $next($request);
    }

    private function validateApiKey(User $user, string $apiKey): bool
    {
        return $user["secret_key"] == $apiKey;
    }

    private function getUser(string $user): ?User
    {
        return User::where('email', $user)->first();
    }

    private function validateRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'user' => 'required',
                'api-key' => 'required'
            ]);
        } catch (ValidationException $exception) {
            throw new HttpResponseException(response()->json([
                'error' => $exception->getMessage(),
                'errors' => $exception->errors()
            ], Response::HTTP_BAD_REQUEST));
        }
    }
}