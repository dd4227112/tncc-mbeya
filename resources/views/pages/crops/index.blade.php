<!-- <body data-layout="horizontal"> -->
<x-page.header />
<x-page.sidebar />

<div class="page-content">
  <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="mb-sm-0 font-size-18">Crops</h4>

          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
              <li class="breadcrumb-item active">Crops</li>
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
                    @if (hasPermission('crops.create'))
                      <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target=".add-crop-modal"><i class="bx bx-plus me-1"></i> Add New</a>
                    @endif
                    @include('pages.crops.add')
                    @include('pages.crops.edit')
                  </div>
                </div>
                <!-- end row -->

                <div class="table-responsive mb-4">
                  <table id="crops-table" class="table align-middle dt-responsive table-check nowrap"
                    style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Unit</th>
                        <th scope="col">Price</th>
                        <th style="width: 180px; min-width: 180px;">Action</th>
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
        @include('pages.crops.scripts.index')
        @include('pages.crops.scripts.add')
        @include('pages.crops.scripts.edit-delete')
      @endpush

      <x-page.footer />
