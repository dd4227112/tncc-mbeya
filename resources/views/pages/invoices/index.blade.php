<!-- <body data-layout="horizontal"> -->
<x-page.header />
<x-page.sidebar />

<div class="page-content">
  <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="mb-sm-0 font-size-18">Invoices</h4>

          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
              <li class="breadcrumb-item active">Invoices</li>
            </ol>
          </div>

        </div>
      </div>
    </div>
    <!-- end page title -->
    <!-- Page content goes here -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-auto">
                <div class="d-flex align-items-center gap-1 mb-4">
                  <div class="input-group datepicker-range">
                    <input type="text" class="form-control flatpickr-input" data-input aria-describedby="date1">
                    <button class="input-group-text" id="date1" data-toggle><i
                        class="bx bx-calendar-event"></i></button>
                  </div>
                  <div class="dropdown">
                    <a class="btn btn-link text-muted py-1 font-size-16 shadow-none dropdown-toggle" href="#"
                      role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bx bx-dots-horizontal-rounded"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li><a class="dropdown-item" href="#">Action</a></li>
                      <li><a class="dropdown-item" href="#">Another action</a></li>
                      <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="col-sm-auto ms-auto">
                <div class="mb-4">
                  <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target=".add-invoice-modal"><i class="bx bx-plus me-1"></i> Add New</a>
                </div>
              </div>
            </div>

            <!-- end row -->
            <div class="table-responsive">
              <table id="invoices-table" class="table align-middle datatable dt-responsive table-check nowrap"
                style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
                <thead>
                  <tr class="bg-transparent">
                    <th>#</th>
                    <th style="width: 120px;">Invoice ID</th>
                    <th>Date</th>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th style="width: 90px;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Invoices data will be populated here via AJAX -->

                </tbody>
              </table>
            </div>
            <!-- end table responsive -->
          </div>
          <!-- end card body -->
        </div>
        <!-- end card -->
      </div>
      <!-- end col -->
    </div>

    <!-- end page content -->

  </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
{{-- Include the modal for adding a new invoice  --}}
@include('pages.invoices.add')

{{-- Include the modal for viewing invoice details --}}
@include('pages.invoices.view')

@push('scripts')
@include('pages.invoices.scripts.index')
@include('pages.invoices.scripts.create-invoice')
@endpush
<x-page.footer />
