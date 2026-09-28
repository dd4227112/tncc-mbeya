<x-auth.header />

<div class="text-center mb-4">
  <h5 class="mb-2">Forgot Password?</h5>
  <p class="text-muted mb-0">Enter your registered phone number and we’ll send you a verification code by SMS.</p>
</div>

@if (session('status'))
  <div class="alert alert-success mb-3" role="alert">
    {{ session('status') }}
  </div>
@endif

<form method="POST" action="{{ route('password.otp.send') }}" class="mt-3">
  @csrf
  <div class="mb-3">
    <label for="phone" class="form-label">Phone Number</label>
    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
      value="{{ old('phone', '+255') }}" placeholder="+255712345678" maxlength="13" required autofocus>
    @error('phone')
      <span class="text-danger mt-1 d-block">{{ $message }}</span>
    @enderror
  </div>

  <div class="mb-3">
    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Send Code</button>
  </div>

  <div class="text-center mt-3">
    <a href="{{ route('login') }}" class="text-muted">Back to login</a>
  </div>
</form>

<x-auth.footer />
