<div class="modal fade edit-user-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit {{ ucfirst($role) }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editUserForm" novalidate>
          <input type="hidden" id="edit-user-id" name="user_id" />
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="edit-first-name">First Name</label>
                <input type="text" id="edit-first-name" name="first_name" class="form-control" />
                <span class="invalid-feedback d-block" id="edit-first-name-error"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="edit-last-name">Last Name</label>
                <input type="text" id="edit-last-name" name="last_name" class="form-control" />
                <span class="invalid-feedback d-block" id="edit-last-name-error"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="edit-phone">Phone</label>
                <input type="text" id="edit-phone" name="phone" class="form-control" />
                <span class="invalid-feedback d-block" id="edit-phone-error"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="edit-email">Email</label>
                <input type="email" id="edit-email" name="email" class="form-control" />
                <span class="invalid-feedback d-block" id="edit-email-error"></span>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="edit-address">Address</label>
                <input type="text" id="edit-address" name="address" class="form-control" />
                <span class="invalid-feedback d-block" id="edit-address-error"></span>
              </div>
            </div>
          </div>
          @if ($role === 'staffs')
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="edit-role">Role</label>
                  <select id="edit-role" name="role_id" class="form-control">
                    <option value="">Select Role</option>
                    @foreach ($roles as $role)
                      <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                  </select>
                  <span class="invalid-feedback d-block" id="edit-role-error"></span>
                </div>
              </div>
            </div>
          @else
            <input type="hidden" name="role_id" value="4" /> <!--  role_id 4 corresponds to 'Member' -->
          @endif
          <div class="form-group">
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
