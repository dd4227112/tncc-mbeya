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
            @include('pages.invoices.filter')

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

{{-- Include the modal for adding a payment --}}
@include('pages.invoices.add-payment')

{{-- Include the modal for adding new user while creating invoice --}}
@include('pages.users.add')

{{-- Include the modal for adding new crop while creating invoice --}}
@include('pages.crops.add')

@push('scripts')
  @include('pages.invoices.scripts.index')
  @include('pages.invoices.scripts.create-invoice')
  @include('pages.invoices.scripts.payment')
  @include('pages.crops.scripts.add')
  @include('pages.users.scripts.add')
@endpush
<x-page.footer />
