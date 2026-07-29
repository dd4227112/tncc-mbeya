<div class="modal fade update-role-modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title ">Update User Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="updateroleForm" novalidate>

          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="current-role">Current Roles</label>
                <input type="text" id="current-role" class="form-control" readonly disabled>
                <span class="invalid-feedback d-block" id="update-current-role-error"></span>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="update-roles">Roles</label>
                <select id="update-roles" name="roles[]" class="form-control update-roles" multiple>
                  @foreach ($all_roles as $roleOption)
                    <option value="{{ $roleOption->id }}">{{ ucfirst($roleOption->name) }}</option>
                  @endforeach
                </select>
                <span class="invalid-feedback d-block" id="update-roles-error"></span>
                <input type="hidden" name="userId" id="userId">
              </div>
            </div>
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-primary float-end">
              <i class="mdi mdi-content-save me-1"></i>Save
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
