<?php

namespace App\Services;

use App\Models\Debtor;
use App\Models\OneTimePassword;
use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PasswordCreatedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordService
{
    public function generateToken(Debtor $debtor): string
    {
        $token = rtrim(strtr(base64_encode(random_bytes(64)), '+/', '-_'), '=');;

        $this->linkTokenToDebtor($debtor, $token);

        return $token;
    }

    private function linkTokenToDebtor(Debtor $debtor, string $token)
    {
        return PasswordReset::updateOrCreate(
            [
                "debtor_id" => $debtor->id
            ],
            [
                "debtor_id" => $debtor->id,
                "token" => $token
            ]
        );
    }

    public function notifyPasswordResetLink(string $token, Debtor $debtor): void
    {
        if (isset($debtor->email) && !is_null($debtor->email)) {
            // TODO notify with password reset link
            Notification::send($debtor->email, new PasswordCreatedNotification($token, $debtor));
        }
    }

    public function notifyPasswordResetted(Debtor $debtor): void
    {
        // TODO notify the user that their password has been changed
    }

    public function verifyTokenExpiry(string $token)
    {
        $passwordReset = PasswordReset::where('token', $token)->with('debtor')->first();

        if (!$passwordReset) {
            return false;
        }

        $valid = Carbon::now()->diffInHours($passwordReset->updated_at) < env("MAX_PASSWORD_TOKEN_DURATION", 24);

        if (!$valid) {
            return false;
        }

        return $passwordReset->debtor;
    }
}
