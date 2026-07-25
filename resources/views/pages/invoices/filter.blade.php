<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body p-4">
        <div class="row">
          <div class="col-lg-10">
            <div class="mt-4">
              <form class="row gx-3 gy-2 align-items-center" id="filterForm" method="POST">
                <div class="col-sm-3">
                  <div class="input-group datepicker-range">
                    <input type="text" placeholder="Select Date Ranges" name="date_range"
                      class="form-control flatpickr-input" data-input aria-describedby="date1">
                  </div>
                </div>
                <div class="col-sm-2">
                  <select class="form-control" data-trigger name="status" id="invoice-status-filter"
                    placeholder="Choose status">
                    <option value="">Choose status</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="overdue">Overdue</option>
                  </select>
                </div>
                <div class="col-auto">
                  <button type="submit" class="btn btn-primary"><i class="bx bx-search-alt align-middle"></i>Filter</button>
                </div>
              </form>
            </div>
          </div>
          <div class="col-lg-2 ms-lg-auto">
            <div class="mt-4">
              @if (hasPermission('invoices.create') )
              <a href="#" class="btn btn-primary float-end" data-bs-toggle="modal"
                data-bs-target=".add-invoice-modal"><i class="bx bx-plus me-1"></i> Add New</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
