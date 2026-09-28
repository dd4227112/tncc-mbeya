<?php

namespace Tests\Feature\Auth;

use App\Jobs\NotifiyUser;
use App\Models\Message;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private const PHONE = '+255712345678';

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    private function requestCode(): string
    {
        $this->post('/forgot-password', ['phone' => self::PHONE])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('password.otp'));

        preg_match('/\b(\d{5})\b/', Message::latest('id')->first()->body, $matches);

        return $matches[1];
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $this->get('/forgot-password')->assertStatus(200);
    }

    public function test_invalid_phone_format_is_rejected(): void
    {
        $this->post('/forgot-password', ['phone' => '0712345678'])->assertSessionHasErrors('phone');
    }

    public function test_unregistered_phone_is_rejected(): void
    {
        $this->post('/forgot-password', ['phone' => self::PHONE])->assertSessionHasErrors('phone');

        Queue::assertNothingPushed();
    }

    public function test_otp_is_generated_hashed_and_sent_by_sms(): void
    {
        User::factory()->create(['phone' => self::PHONE]);

        $code = $this->requestCode();

        $otp = PasswordResetOtp::where('phone', self::PHONE)->sole();
        $this->assertNotSame($code, $otp->code);
        $this->assertTrue(Hash::check($code, $otp->code));
        $this->assertEqualsWithDelta(now()->addMinutes(5)->timestamp, $otp->expires_at->timestamp, 5);
        Queue::assertPushedOn('sms-notifications', NotifiyUser::class);
        $this->get('/verify-otp')->assertStatus(200);
    }

    public function test_wrong_code_is_rejected(): void
    {
        User::factory()->create(['phone' => self::PHONE]);
        $code = $this->requestCode();

        $this->post('/verify-otp', ['code' => $code === '00000' ? '11111' : '00000'])
            ->assertSessionHasErrors('code');

        $this->assertSame(1, PasswordResetOtp::where('phone', self::PHONE)->sole()->attempts);
    }

    public function test_expired_code_is_rejected(): void
    {
        User::factory()->create(['phone' => self::PHONE]);
        $code = $this->requestCode();

        $this->travel(6)->minutes();

        $this->post('/verify-otp', ['code' => $code])->assertSessionHasErrors('code');
    }

    public function test_code_is_locked_after_too_many_attempts(): void
    {
        User::factory()->create(['phone' => self::PHONE]);
        $code = $this->requestCode();
        PasswordResetOtp::where('phone', self::PHONE)->update(['attempts' => 5]);

        $this->post('/verify-otp', ['code' => $code])->assertSessionHasErrors('code');
    }

    public function test_new_password_screen_requires_verified_code(): void
    {
        $this->get('/reset-password')->assertRedirect(route('password.request'));
        $this->post('/reset-password', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect(route('password.request'));
    }

    public function test_password_can_be_reset_with_valid_code(): void
    {
        $user = User::factory()->create(['phone' => self::PHONE]);
        $code = $this->requestCode();

        $this->post('/verify-otp', ['code' => $code])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('password.reset'));

        $this->get('/reset-password')->assertStatus(200);

        $this->post('/reset-password', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasNoErrors()->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->assertSame(0, PasswordResetOtp::count());
        $this->get('/reset-password')->assertRedirect(route('password.request'));
    }

    public function test_resend_is_throttled_by_cooldown(): void
    {
        User::factory()->create(['phone' => self::PHONE]);
        $this->requestCode();

        $this->post('/verify-otp/resend')->assertSessionHasErrors('code');

        $this->travel(61)->seconds();
        $this->post('/verify-otp/resend')->assertSessionHasNoErrors();
        Queue::assertPushed(NotifiyUser::class, 2);
    }
}
