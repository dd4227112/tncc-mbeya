<?php

namespace App\Services;

use App\Jobs\NotifiyUser;
use App\Models\Message;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordResetOtpService
{
    public const CODE_LENGTH = 5;
    public const EXPIRES_IN_MINUTES = 5;
    public const MAX_ATTEMPTS = 5;
    public const RESEND_COOLDOWN_SECONDS = 60;

    /**
     * Generate a new OTP for the user, replacing any previous one, and queue it by SMS.
     */
    public function issue(User $user): void
    {
        $code = str_pad((string) random_int(0, 10 ** self::CODE_LENGTH - 1), self::CODE_LENGTH, '0', STR_PAD_LEFT);

        PasswordResetOtp::where('phone', $user->phone)->delete();
        PasswordResetOtp::create([
            'phone' => $user->phone,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(self::EXPIRES_IN_MINUTES),
        ]);

        $message = Message::create([
            'phone' => $user->phone,
            'body' => "Habari {$user->first_name}, namba yako ya uthibitisho (OTP) ya kubadilisha nenosiri ni {$code}. Itaisha muda baada ya dakika " . self::EXPIRES_IN_MINUTES . ". Usimpe mtu yeyote namba hii.",
            'status' => 'pending',
            'reference' => 'password-reset',
        ]);
        NotifiyUser::dispatch($message->id)->onQueue('sms-notifications');
    }

    /**
     * Seconds the user must still wait before another code can be sent (0 when allowed).
     */
    public function secondsUntilResend(string $phone): int
    {
        $latest = PasswordResetOtp::where('phone', $phone)->latest('id')->first();
        if (!$latest) {
            return 0;
        }

        return max(0, self::RESEND_COOLDOWN_SECONDS - (int) $latest->created_at->diffInSeconds(now()));
    }

    /**
     * Verify a code for the phone number. Returns null on success, or an error message.
     * A successfully verified code is consumed and cannot be reused.
     */
    public function verify(string $phone, string $code): ?string
    {
        $otp = PasswordResetOtp::where('phone', $phone)->latest('id')->first();

        if (!$otp || $otp->isExpired()) {
            return 'This code has expired. Please request a new code.';
        }
        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            return 'Too many incorrect attempts. Please request a new code.';
        }
        if (!Hash::check($code, $otp->code)) {
            $otp->increment('attempts');
            return 'The code you entered is incorrect.';
        }

        PasswordResetOtp::where('phone', $phone)->delete();

        return null;
    }
}
