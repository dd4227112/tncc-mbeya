<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Minutes a verified OTP session remains valid for setting a new password.
     */
    private const VERIFIED_SESSION_MINUTES = 10;

    /**
     * Display the create new password view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (!$this->verifiedUser($request)) {
            return $this->restart($request);
        }

        return view('auth.reset-password');
    }

    /**
     * Save the new password for the phone number verified by OTP.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $this->verifiedUser($request);
        if (!$user) {
            return $this->restart($request);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        $request->session()->forget('password_reset');

        return redirect()->route('login')
            ->with('status', 'Your password has been reset. You can now log in with your new password.');
    }

    /**
     * The user whose phone number was verified by OTP in this session, if still valid.
     */
    private function verifiedUser(Request $request): ?User
    {
        $phone = $request->session()->get('password_reset.phone');
        $verifiedAt = $request->session()->get('password_reset.verified_at');

        if (!$phone || !$verifiedAt || now()->timestamp - $verifiedAt > self::VERIFIED_SESSION_MINUTES * 60) {
            return null;
        }

        return User::where('phone', $phone)->first();
    }

    private function restart(Request $request): RedirectResponse
    {
        $request->session()->forget('password_reset');

        return redirect()->route('password.request')
            ->withErrors(['phone' => 'Your reset session has expired. Please request a new code.']);
    }
}
