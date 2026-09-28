<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordOtpController extends Controller
{
    /**
     * Display the enter-OTP view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $phone = $request->session()->get('password_reset.phone');
        if (!$phone) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', [
            'maskedPhone' => substr($phone, 0, 4) . str_repeat('*', 6) . substr($phone, -3),
            'expiresIn' => PasswordResetOtpService::EXPIRES_IN_MINUTES,
            'codeLength' => PasswordResetOtpService::CODE_LENGTH,
        ]);
    }

    /**
     * Verify the OTP against the phone number held in the session.
     *
     * @throws ValidationException
     */
    public function store(Request $request, PasswordResetOtpService $otp): RedirectResponse
    {
        $phone = $request->session()->get('password_reset.phone');
        if (!$phone) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'code' => ['required', 'digits:' . PasswordResetOtpService::CODE_LENGTH],
        ]);

        $error = $otp->verify($phone, $request->input('code'));
        if ($error) {
            throw ValidationException::withMessages(['code' => $error]);
        }

        $request->session()->regenerate();
        $request->session()->put('password_reset.verified_at', now()->timestamp);

        return redirect()->route('password.reset');
    }

    /**
     * Send a fresh OTP to the phone number held in the session.
     *
     * @throws ValidationException
     */
    public function resend(Request $request, PasswordResetOtpService $otp): RedirectResponse
    {
        $phone = $request->session()->get('password_reset.phone');
        $user = $phone ? User::where('phone', $phone)->first() : null;
        if (!$user) {
            return redirect()->route('password.request');
        }

        $wait = $otp->secondsUntilResend($phone);
        if ($wait > 0) {
            throw ValidationException::withMessages([
                'code' => "Please wait {$wait} seconds before requesting another code.",
            ]);
        }

        $otp->issue($user);

        return back()->with('status', 'A new code has been sent to your phone.');
    }
}
