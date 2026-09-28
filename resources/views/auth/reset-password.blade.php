<x-auth.header />

<div class="text-center mb-4">
  <h5 class="mb-2">Create New Password</h5>
  <p class="text-muted mb-0">Your phone number has been verified. Choose a new password for your account.</p>
</div>

<form method="POST" action="{{ route('password.store') }}" class="mt-3">
  @csrf
  <div class="mb-3">
    <label for="password" class="form-label">New Password</label>
    <div class="input-group auth-pass-inputgroup">
      <input id="password" name="password" type="password"
        class="form-control @error('password') is-invalid @enderror" placeholder="Enter new password"
        autocomplete="new-password" required autofocus>
      <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon"><i
          class="mdi mdi-eye-outline"></i></button>
    </div>
    @error('password')
      <span class="text-danger mt-1 d-block">{{ $message }}</span>
    @enderror
  </div>

  <div class="mb-3">
    <label for="password_confirmation" class="form-label">Confirm Password</label>
    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
      placeholder="Re-enter new password" autocomplete="new-password" required>
  </div>

  <div class="mb-3">
    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Reset Password</button>
  </div>

  <div class="text-center mt-3">
    <a href="{{ route('login') }}" class="text-muted">Back to login</a>
  </div>
</form>

<x-auth.footer />
