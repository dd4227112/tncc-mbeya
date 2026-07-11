  <div class="modal fade add-unit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Unit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addUnitForm" novalidate>
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="add-name">Name</label>
                  <input type="text" id="add-name" name="name" class="form-control" />
                  <span class="invalid-feedback d-block" id="add-name-error"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="add-abbreviation">Abbreviation</label>
                  <input type="text" id="add-abbreviation" name="abbreviation" class="form-control" />
                  <span class="invalid-feedback d-block" id="add-abbreviation-error"></span>
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
