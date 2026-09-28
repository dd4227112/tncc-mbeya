<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasswordResetOtpService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the forgot password (enter phone number) view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset OTP to the user's registered phone number.
     *
     * @throws ValidationException
     */
    public function store(Request $request, PasswordResetOtpService $otp): RedirectResponse
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                'size:13',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (! isValidPhone($value)) {
                        $fail('The :attribute must be a valid Tanzanian phone number, e.g. +255712345678.');
                    }
                },
            ],
        ]);

        $user = User::where('phone', $request->input('phone'))->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'phone' => 'We could not find an account with that phone number.',
            ]);
        }

        $wait = $otp->secondsUntilResend($user->phone);
        if ($wait > 0) {
            throw ValidationException::withMessages([
                'phone' => "Please wait {$wait} seconds before requesting another code.",
            ]);
        }

        $otp->issue($user);

        $request->session()->forget('password_reset');
        $request->session()->put('password_reset.phone', $user->phone);

        return redirect()->route('password.otp')
            ->with('status', 'A ' . PasswordResetOtpService::CODE_LENGTH . '-digit code has been sent to your phone.');
    }
}
