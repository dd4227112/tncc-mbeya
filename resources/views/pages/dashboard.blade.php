<x-page.header />
<x-page.sidebar />

<div class="page-content">
  <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>

        </div>
      </div>
    </div>
    <!-- end page title -->
    <div class="row">
      <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
          <!-- card body -->
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-6">
                <span class="text-muted mb-3 lh-1 d-block text-truncate">Members</span>
                <h4 class="mb-3">
                  <span class="counter-value" data-target="865.2">0</span>
                </h4>
              </div>
            </div>
          </div><!-- end card body -->
        </div><!-- end card -->
      </div><!-- end col -->

      <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
          <!-- card body -->
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-6">
                <span class="text-muted mb-3 lh-1 d-block text-truncate">Crops</span>
                <h4 class="mb-3">
                  <span class="counter-value" data-target="6258">0</span>
                </h4>
              </div>
            </div>
          </div><!-- end card body -->
        </div><!-- end card -->
      </div><!-- end col-->

      <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
          <!-- card body -->
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-6">
                <span class="text-muted mb-3 lh-1 d-block text-truncate">Invoices</span>
                <h4 class="mb-3">
                  <span class="counter-value" data-target="4.32">0</span>
                </h4>
              </div>
            </div>
          </div><!-- end card body -->
        </div><!-- end card -->
      </div><!-- end col -->

      <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
          <!-- card body -->
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-6">
                <span class="text-muted mb-3 lh-1 d-block text-truncate">Payments</span>
                <h4 class="mb-3">
                  <span class="counter-value" data-target="12.57">0</span>
                </h4>
              </div>
            </div>
          </div><!-- end card body -->
        </div><!-- end card -->
      </div><!-- end col -->
    </div><!-- end row-->

    <div class="row">
      <div class="col-xl-6">
        <!-- card -->
        <div class="card card-h-100">
          <!-- card body -->
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center mb-4">
              <h5 class="card-title me-2">Transaction Status</h5>
            </div>

            <div class="row align-items-center">
              <div class="col-sm">
                <div id="wallet-balance" data-colors='["#777aca", "#5156be", "#a8aada"]' class="apex-charts"></div>
              </div>
              <div class="col-sm align-self-center">
                <div class="mt-4 mt-sm-0">
                  <div>
                    <p class="mb-2"><i class="mdi mdi-circle align-middle font-size-10 me-2 text-success"></i>
                      Completed</p>
                    <h6>TZS 4025.32</span></h6>
                  </div>

                  <div class="mt-4 pt-2">
                    <p class="mb-2"><i class="mdi mdi-circle align-middle font-size-10 me-2 text-primary"></i>
                      Pending</p>
                    <h6>TZS 1123.64</span></h6>
                  </div>

                  <div class="mt-4 pt-2">
                    <p class="mb-2"><i class="mdi mdi-circle align-middle font-size-10 me-2 text-info"></i> Failed
                    </p>
                    <h6>TZS 2263.09</span></h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end card -->
      </div>
      <!-- end col -->
      <div class="col-xl-6">
        <div class="row">
          <div class="col-xl-12">
            <!-- card -->
            <div class="card card-h-100">
              <!-- card body -->
              <div class="card-body">
                <div class="d-flex flex-wrap align-items-center mb-4">
                  <h5 class="card-title me-2">Invoice Payment</h5>
                </div>
                <div class="row align-items-center">
                  <div class="col-sm">
                    <div id="invested-overview" data-colors='["#5156be", "#34c38f"]' class="apex-charts"></div>
                  </div>
                  <div class="col-sm align-self-center">
                    <div class="mt-4 mt-sm-0">
                      <p class="mb-1">Total Invoiced</p>
                      <h4>TZS 6134.39</h4>

                      <div class="row g-0">
                        <div class="col-6">
                          <div>
                            <p class="mb-2 text-muted text-uppercase font-size-11">Paid</p>
                            <h5 class="fw-medium">TZS 2632.46</h5>
                          </div>
                        </div>
                        <div class="col-6">
                          <div>
                            <p class="mb-2 text-muted text-uppercase font-size-11">Pending</p>
                            <h5 class="fw-medium">-TZS 924.38</h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- end col -->
        </div>
        <!-- end row -->
      </div>
      <!-- end col -->
    </div> <!-- end row-->

    <div class="row">
      <div class="col-xl-8">
        <!-- card -->
        <div class="card">
          <!-- card body -->
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center mb-4">
              <h5 class="card-title me-2">Month-wise Invoice Payments</h5>

            </div>

            <div class="row align-items-center">
              <div class="col-xl-12">
                <div>
                  <div id="market-overview" data-colors='["#5156be", "#34c38f"]' class="apex-charts"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- end card -->
        </div>
        <!-- end col -->
      </div>
      <!-- end row-->

      <div class="col-xl-4">
        <!-- card -->
        <div class="card">
          <!-- card body -->
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center mb-4">
              <h5 class="card-title me-2">Crops collections</h5>
            </div>

            <div class="px-2 py-2">
              <p class="mb-1">Mchele <span class="float-end">75%</span></p>
              <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 75%"
                  aria-valuenow="75" aria-valuemin="0" aria-valuemax="75">
                </div>
              </div>

              <p class="mt-3 mb-1">Karanga <span class="float-end">55%</span></p>
              <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 55%"
                  aria-valuenow="55" aria-valuemin="0" aria-valuemax="55">
                </div>
              </div>

              <p class="mt-3 mb-1">Mahindi <span class="float-end">85%</span></p>
              <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 85%"
                  aria-valuenow="85" aria-valuemin="0" aria-valuemax="85">
                </div>
              </div>
              <p class="mt-3 mb-1">Korosho <span class="float-end">85%</span></p>
              <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 85%"
                  aria-valuenow="85" aria-valuemin="0" aria-valuemax="85">
                </div>
              </div>
            </div>
          </div>
          <!-- end card body -->
        </div>
        <!-- end card -->
      </div>
      <!-- end col -->
    </div>
    <!-- end row-->

    <div class="row">
      <div class="col-xl-6">
        <div class="card">
          <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Recent Transactions</h4>
          </div><!-- end card header -->

          <div class="card-body px-0">
            <div class="table-responsive px-3" data-simplebar style="max-height: 352px;">
              <table class="table align-middle table-nowrap table-borderless">
                <tbody>
                  <tr>
                    <td style="width: 50px;">
                      <div class="font-size-22 text-success">
                        <i class="bx bx-down-arrow-circle d-block"></i>
                      </div>
                    </td>

                    <td>
                      <div>
                        <h5 class="font-size-14 mb-1">Buy BTC</h5>
                        <p class="text-muted mb-0 font-size-12">14 Mar, 2021</p>
                      </div>
                    </td>

                    <td>
                      <div class="text-end">
                        <h5 class="font-size-14 mb-0">0.016 BTC</h5>
                        <p class="text-muted mb-0 font-size-12">Coin Value</p>
                      </div>
                    </td>

                    <td>
                      <div class="text-end">
                        <h5 class="font-size-14 text-muted mb-0">TZS125.20</h5>
                        <p class="text-muted mb-0 font-size-12">Amount</p>
                      </div>
                    </td>
                  </tr>



                </tbody>
              </table>
            </div>
          </div>
          <!-- end card body -->
        </div>
        <!-- end card -->
      </div>
      <!-- end col -->

      <div class="col-xl-6">
        <div class="card">
          <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Recent Invoices</h4>

          </div><!-- end card header -->

          <div class="card-body px-0">
            <div class="table-responsive px-3" data-simplebar style="max-height: 352px;">
              <table class="table align-middle table-nowrap table-borderless">
                <tbody>
                  <tr>
                    <td style="width: 50px;">
                      <div class="font-size-22 text-success">
                        <i class="bx bx-down-arrow-circle d-block"></i>
                      </div>
                    </td>

                    <td>
                      <div>
                        <h5 class="font-size-14 mb-1">Buy BTC</h5>
                        <p class="text-muted mb-0 font-size-12">14 Mar, 2021</p>
                      </div>
                    </td>

                    <td>
                      <div class="text-end">
                        <h5 class="font-size-14 mb-0">0.016 BTC</h5>
                        <p class="text-muted mb-0 font-size-12">Coin Value</p>
                      </div>
                    </td>

                    <td>
                      <div class="text-end">
                        <h5 class="font-size-14 text-muted mb-0">TZS125.20</h5>
                        <p class="text-muted mb-0 font-size-12">Amount</p>
                      </div>
                    </td>
                  </tr>



                </tbody>
              </table>
            </div>
          </div>
          <!-- end card body -->
        </div>
        <!-- end card -->
      </div>
      <!-- end col -->
    </div><!-- end row -->

    <!-- end page content -->

  </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

<x-page.footer />
<!-- pace js -->
<script src="{{ asset('assets/libs/pace-js/pace.min.js') }}"></script>

<!-- apexcharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

</script>
<!-- dashboard init -->

<script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>
{{-- @push('script')
  @include('pages.dashboard-js')
@endpush --}}
