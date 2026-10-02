  <div class="modal fade add-crop-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Crop</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addCropForm" novalidate>
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
                  <label for="add-description">Description</label>
                  <input type="text" id="add-description" name="description" class="form-control" />
                  <span class="invalid-feedback d-block" id="add-description-error"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="add-price">Price</label>
                  <input type="number" id="add-price" name="price" class="form-control" value="5" />
                  <span class="invalid-feedback d-block" id="add-price-error"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="add-unit">Unit</label>
                  <select id="add-unit" name="unit_id" class="form-control">
                    <option value="">Select Unit</option>
                    @foreach ($units as $unit)
                      <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                  </select>
                  <span class="invalid-feedback d-block" id="add-unit-error"></span>
                </div>
              </div>
            </div>
            @if (hasPermission('crops.create'))
              <div class="form-group">
                <button type="submit" class="btn btn-primary float-end"> <i
                    class="mdi mdi-content-save me-1"></i>Save</button>
              </div>
            @endif
          </form>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
