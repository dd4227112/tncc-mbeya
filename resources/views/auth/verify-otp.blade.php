<x-auth.header />

<div class="text-center mb-4">
  <h5 class="mb-2">Enter Verification Code</h5>
  <p class="text-muted mb-0">We sent a {{ $codeLength }}-digit code to <strong>{{ $maskedPhone }}</strong>.
    The code expires in {{ $expiresIn }} minutes.</p>
</div>

@if (session('status'))
  <div class="alert alert-success mb-3" role="alert">
    {{ session('status') }}
  </div>
@endif

<form method="POST" action="{{ route('password.otp.verify') }}" class="mt-3">
  @csrf
  <div class="mb-3">
    <label for="code" class="form-label">Verification Code</label>
    <input type="text" class="form-control text-center fs-4 @error('code') is-invalid @enderror" id="code"
      name="code" inputmode="numeric" pattern="[0-9]{{ '{' . $codeLength . '}' }}" maxlength="{{ $codeLength }}"
      autocomplete="one-time-code" placeholder="{{ str_repeat('•', $codeLength) }}" style="letter-spacing: .6em;"
      required autofocus>
    @error('code')
      <span class="text-danger mt-1 d-block">{{ $message }}</span>
    @enderror
  </div>

  <div class="mb-3">
    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Verify Code</button>
  </div>
</form>

<form method="POST" action="{{ route('password.otp.resend') }}" class="text-center">
  @csrf
  <span class="text-muted">Didn’t receive the code?</span>
  <button type="submit" class="btn btn-link p-0 align-baseline">Resend code</button>
</form>

<div class="text-center mt-3">
  <a href="{{ route('password.request') }}" class="text-muted">Use a different phone number</a>
</div>

<x-auth.footer />
