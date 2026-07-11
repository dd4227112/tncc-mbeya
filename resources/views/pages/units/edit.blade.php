  <div class="modal fade edit-unit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Unit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editUnitForm" novalidate>
            <input type="hidden" id="edit-unit-id" name="unit_id" />
            <div class="row">
              <div class="col-xl-12 col-md-12">
                <div class="form-group mb-3">
                  <label for="edit-name">Name</label>
                  <input type="text" id="edit-name" name="name" required data-pristine-required-message="Please Enter a name"
                    class="form-control" />
                  <span class="invalid-feedback d-block" id="edit-name-error"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xl-12 col-md-12">
                <div class="form-group mb-3">
                  <label for="edit-abbreviation">Abbreviation</label>
                  <input type="text" id="edit-abbreviation" name="abbreviation" required
                    data-pristine-required-message="Please Enter an abbreviation" class="form-control" />
                  <span class="invalid-feedback d-block" id="edit-abbreviation-error"></span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary float-end"> <i class="mdi mdi-content-save me-1"></i>Save</button>
            </div>
          </form>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
