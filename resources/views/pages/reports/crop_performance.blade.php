<!-- <body data-layout="horizontal"> -->
<x-page.header />
  <!-- DataTables -->
  <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />

<x-page.sidebar />

<div class="page-content">
  <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="mb-sm-0 font-size-18">Crop Performance Report <span id ="reportDate"></span></h4>

          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
              <li class="breadcrumb-item active">Reports</li>
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

            <div class="row align-items-center">
              <div class="col-md-12">
                @include('pages.reports.filter')
              </div>
            </div>
            <!-- end row -->
            <div class="table-responsive">
              <table id="datatable-buttons" class="table align-middle dt-responsive table-check nowrap"
                style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Crop</th>
                    <th scope="col">Members</th>
                    <th scope="col">Total Weight</th>
                    <th scope="col">Average Weight</th>
                    <th scope="col">Total Amount</th>
                  </tr>

                </thead>
                <tbody id="crops-table-body">
                  <!-- Crops data will be populated here via AJAX -->

                </tbody>
              </table>
              <!-- end table -->
            </div>
            <!-- end page content -->

          </div> <!-- container-fluid -->
        </div>
      </div>
    </div>
  </div>
  <!-- End Page-content -->

  @push('scripts')
    @include('pages.reports.scripts.crop-performance')
  @endpush
  <x-page.footer />
  <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
  <script src="{{ asset('assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
  <script src="{{ asset('assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
  <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
  <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
  <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
