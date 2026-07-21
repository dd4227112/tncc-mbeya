<div class="modal fade profile-modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">My Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateProfileForm" novalidate>
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="add-first-name">First Name</label>
                <input type="text" id="add-first-name" name="first_name" class="form-control" />
                <span class="invalid-feedback d-block" id="add-first-name-error"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="add-last-name">Last Name</label>
                <input type="text" id="add-last-name" name="last_name" class="form-control" />
                <span class="invalid-feedback d-block" id="add-last-name-error"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="add-phone">Phone</label>
                <input type="text" id="add-phone" name="phone" class="form-control" />
                <span class="invalid-feedback d-block" id="add-phone-error"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="add-email">Email</label>
                <input type="email" id="add-email" name="email" class="form-control" />
                <span class="invalid-feedback d-block" id="add-email-error"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="add-address">Address</label>
                <input type="text" id="add-address" name="address" class="form-control" />
                <span class="invalid-feedback d-block" id="add-address-error"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="add-role">Role</label>
                <input type="text" id="add-role" readonly disabled class="form-control" />
                <span class="invalid-feedback d-block" id="add-role-error"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
             {{-- add a close button --}}
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary float-end"> <i
                class="mdi mdi-content-save me-1"></i>Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
