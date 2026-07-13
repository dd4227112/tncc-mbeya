  <div class="modal fade change-password-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create new Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="changePasswordForm" action="{{ route('password.update') }}" method="POST" novalidate>
            @csrf
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="update_password_current_password">Current Password</label>
                  <input type="password" id="update_password_current_password" name="current_password"
                    class="form-control" />
                  <span class="invalid-feedback d-block" id="update_password_current_password-error"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="update_password_password">New Password</label>
                  <input type="password" id="update_password_password" name="password" class="form-control" />
                  <span class="invalid-feedback d-block" id="update_password_password-error"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="update_password_password_confirmation">Confirm Password</label>
                  <input type="password" id="update_password_password_confirmation" name="password_confirmation"
                    class="form-control" />
                  <span class="invalid-feedback d-block" id="update_password_password_confirmation-error"></span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary float-end"> <i
                  class="mdi mdi-content-save me-1"></i>Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    @include('pages.global-js')
  @endpush
