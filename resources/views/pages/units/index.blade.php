<!-- <body data-layout="horizontal"> -->
<x-page.header />
<x-page.sidebar />

<div class="page-content">
  <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="mb-sm-0 font-size-18">Units</h4>

          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
              <li class="breadcrumb-item active">Units</li>
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
                <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3">
                  <div>
                    @if (hasPermission('units.create'))
                      <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target=".add-unit-modal"><i class="bx bx-plus me-1"></i> Add New</a>
                    @endif
                    @include('pages.units.add')
                    @include('pages.units.edit')
                  </div>
                </div>
                <!-- end row -->

                <div class="table-responsive mb-4">
                  <table id="units-table" class="table align-middle dt-responsive table-check nowrap"
                    style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Abbreviation</th>
                        <th style="width: 80px; min-width: 80px;">Action</th>
                      </tr>
                    </thead>
                    <tbody id="units-table-body">
                      <!-- Units data will be populated here via AJAX -->

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
        @include('pages.units.scripts.index')
        @include('pages.units.scripts.add')
        @include('pages.units.scripts.edit-delete')
      @endpush

      <x-page.footer />
