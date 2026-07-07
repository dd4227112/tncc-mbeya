<x-auth.header />
<form method="POST" action="{{ route('login') }}" class="mt-4 pt-2">
  @csrf
  <div class="mb-3">
    <label for="login" class="form-label">Phone Number or Email</label>
    <input type="text" class="form-control @error('login') is-invalid @enderror" id="login" name="login"
      value="{{ old('login') }}" placeholder="Enter phone number or email" required autofocus>
    @error('login')
      <span class="text-danger mt-1 d-block">{{ $message }}</span>
    @enderror
  </div>
  <div class="mb-3">
    <div class="d-flex align-items-start">
      <div class="flex-grow-1">
        <label for="password" class="form-label">Password</label>
      </div>
      <div class="flex-shrink-0">
        <div class="">
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-muted">Forgot password?</a>
          @endif
        </div>
      </div>
    </div>

    <div class="input-group auth-pass-inputgroup">
      <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror"
        placeholder="Enter password" aria-label="Password" aria-describedby="password-addon" required
        autocomplete="current-password">
      <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon"><i
          class="mdi mdi-eye-outline"></i></button>
    </div>
    @error('password')
      <span class="text-danger mt-1 d-block">{{ $message }}</span>
    @enderror
  </div>
  <div class="row mb-4">
    <div class="col">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
        <label class="form-check-label" for="remember_me">
          Remember me
        </label>
      </div>
    </div>

  </div>
  <div class="mb-3">
    <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Log In</button>
  </div>
</form>
<x-auth.footer />
