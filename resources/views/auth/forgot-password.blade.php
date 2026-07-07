<x-auth.header />

<div class="text-center mb-4">
  <h5 class="mb-2">Forgot Password?</h5>
  <p class="text-muted mb-0">No worries, we’ll send reset instructions to your phone number.</p>
</div>

@if (session('status'))
  <div class="alert alert-success mb-3" role="alert">
    {{ session('status') }}
  </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="mt-3">
  @csrf
  <div class="mb-3">
    <label for="phone" class="form-label">Phone Number</label>
    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
      value="{{ old('phone') }}" placeholder="Enter your phone number" required autofocus>
    @error('phone')
      <span class="text-danger mt-1 d-block">{{ $message }}</span>
    @enderror
  </div>

  <div class="mb-3">
    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Send Reset Link</button>
  </div>

  <div class="text-center mt-3">
    <a href="{{ route('login') }}" class="text-muted">Back to login</a>
  </div>
</form>

<x-auth.footer />
