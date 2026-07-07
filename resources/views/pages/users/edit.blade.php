  <div class="modal fade edit-crop-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Crop</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editCropForm" novalidate>
            <input type="hidden" id="edit-crop-id" name="crop_id" />
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
                  <label for="edit-description">Description</label>
                  <input type="text" id="edit-description" name="description" required
                    data-pristine-required-message="Please Enter a description" class="form-control" />
                  <span class="invalid-feedback d-block" id="edit-description-error"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xl-12 col-md-12">
                <div class="form-group mb-3">
                  <label for="edit-price">Price</label>
                  <input type="number" id="edit-price" name="price" required data-pristine-required-message="Please Enter a price"
                    class="form-control" />
                  <span class="invalid-feedback d-block" id="edit-price-error"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xl-12 col-md-12">
                <div class="form-group mb-3">
                  <label for="edit-unit">Unit</label>
                  <select id="edit-unit" name="unit_id" class="form-control">
                    <option value="">Select Unit</option>
                    @foreach ($units as $unit)
                      <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                  </select>
                  <span class="invalid-feedback d-block" id="edit-unit-error"></span>
                </div>
              </div>
            </div>            
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
