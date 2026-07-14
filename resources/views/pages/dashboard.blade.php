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
                  <span class="counter-value" data-target="{{ $members_counts }}">0</span>
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
                  <span class="counter-value" data-target="{{ $crops_counts }}">0</span>
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
                  <span class="" data-target=""> TZS {{ number_format($invoice_paid_amount, 2) }}</span>
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
                  <span class="" data-target="">TZS {{ number_format($paid_amounts, 2) }}</span>
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
                  @forelse ($paymentStatusSummary as $key=> $paymentStatus)
                    <div>
                      <p class="mb-2"><i class="mdi mdi-circle align-middle font-size-10 me-2 text-success"></i>
                        {{ ucfirst($key) }}</p>
                      <h6>TZS {{ number_format($paymentStatus, 2) }}</span></h6>
                    </div>
                  @empty
                  @endforelse
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
                      <h4>TZS {{ number_format($invoice_grand_total, 2) }}</h4>

                      <div class="row g-0">
                        @foreach ($invoice_paid_summary as $key => $summary)
                          <div class="col-6">
                            <div>
                              <p class="mb-2 text-muted text-uppercase font-size-11">{{ ucfirst($key) }}</p>
                              <h5 class="fw-medium">TZS {{ number_format($summary, 2) }}</h5>
                            </div>
                          </div>
                        @endforeach
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
              @forelse ($cropsSummaryData as $cropsSummary)
                <p class="mb-1">{{ $cropsSummary['name'] }} <span
                    class="float-end">{{ $cropsSummary['percentage'] }}%</span></p>
                <div class="progress mt-2" style="height: 6px;">
                  <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                    style="width: {{ $cropsSummary['percentage'] }}%" aria-valuenow="75" aria-valuemin="0"
                    aria-valuemax="75">
                  </div>
                </div>
              @empty
                <p>No data found</p>
              @endforelse
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
            <div class="table-responsive">
              <table id="payments-table" class="table align-middle datatable dt-responsive table-check nowrap"
                style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
                <thead>
                  <tr class="bg-transparent">
                    <th>#</th>
                    <th>Date</th>
                    <th>Payer</th>
                    <th>Amount</th>
                    <th>Txn Reference</th>
                    <th style="width: 120px;">Invoice</th>
                    <th>Paid Through</th>
                    <th>Status</th>
                    <th>Processed By</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($payments as $payment)
                    <tr>
                      <td>{{ $payment['id'] }}</td>
                      <td>{{ $payment['date'] }}</td>
                      <td>{{ $payment['payer'] }}</td>
                      <td>TZS {{ $payment['amount'] }}</td>
                      <td>{{ $payment['reference'] ?? '—' }}</td>
                      <td>{{ $payment['invoice'] ?? '—' }}</td>
                      <td>{{ ucfirst($payment['method']) }}</td>
                      <td>{!! $payment['status_badge'] !!}</td>
                      <td>{{ $payment['processed'] }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="9" class="text-center text-muted py-4">No transactions found</td>
                    </tr>
                  @endforelse

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
                  </tr>
                </thead>
                <tbody>
                  @forelse ($invoices as $invoice)
                    <tr>
                      <td>{{ $invoice['id'] }}</td>
                      <td>{{ $invoice['reference_number'] }}</td>
                      <td>{{ $invoice['date'] }}</td>
                      <td>{{ $invoice['member'] }}</td>
                      <td>TZS {{ $invoice['total_amount'] }}</td>
                      <td>{!! $invoice['status_badge'] !!}</td>
                      <td>{{ $invoice['created_by'] }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="text-center text-muted py-4">No invoices found</td>
                    </tr>
                  @endforelse
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
<script>
  function getChartColorsArray(r) {
    r = $(r).attr("data-colors");
    return (r = JSON.parse(r)).map(function(r) {
      r = r.replace(" ", "");
      if (-1 == r.indexOf("--")) return r;
      r = getComputedStyle(document.documentElement).getPropertyValue(r);
      return r || void 0;
    });
  }

  var piechartColors = getChartColorsArray("#wallet-balance"),
    options = {
      series: [{{ $paymentStatusAmounts }}],
      chart: {
        width: 227,
        height: 227,
        type: "pie"
      },
      labels: [{!! $paymentStatusNames !!}],
      colors: piechartColors,
      stroke: {
        width: 0
      },
      legend: {
        show: !1
      },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200
          }
        }
      }],
    };
  (chart = new ApexCharts(
    document.querySelector("#wallet-balance"),
    options
  )).render();


  var radialchartColors = getChartColorsArray("#invested-overview"),
    options = {
      chart: {
        height: 270,
        type: "radialBar",
        offsetY: -10
      },
      plotOptions: {
        radialBar: {
          startAngle: -130,
          endAngle: 130,
          dataLabels: {
            name: {
              show: !1
            },
            value: {
              offsetY: 10,
              fontSize: "18px",
              color: void 0,
              formatter: function(r) {
                return r + "%";
              },
            },
          },
        },
      },
      colors: [radialchartColors[0]],
      fill: {
        type: "gradient",
        gradient: {
          shade: "dark",
          type: "horizontal",
          gradientToColors: [radialchartColors[1]],
          shadeIntensity: 0.15,
          inverseColors: !1,
          opacityFrom: 1,
          opacityTo: 1,
          stops: [20, 60],
        },
      },
      stroke: {
        dashArray: 4
      },
      legend: {
        show: !1
      },
      series: [{{ $invoice_payment_percentage }}],
      labels: ["Series A"],
    };
  (chart = new ApexCharts(
    document.querySelector("#invested-overview"),
    options
  )).render();


  var barchartColors = getChartColorsArray("#market-overview"),
    options = {
      series: [{
          name: "Invoices",
          data: [
            {{ $invoicesDataArray }}
          ],
        },
        {
          name: "Payments",
          data: [
            {{ $paymentsDataArray }}
          ],
        },
      ],
      chart: {
        type: "bar",
        height: 400,
        stacked: !0,
        toolbar: {
          show: !1
        }
      },
      plotOptions: {
        bar: {
          columnWidth: "20%"
        }
      },
      colors: barchartColors,
      fill: {
        opacity: 1
      },
      dataLabels: {
        enabled: !1
      },
      legend: {
        show: !1
      },
      yaxis: {
        labels: {
          formatter: function(r) {
            return r.toFixed(0);
          },
        },
      },
      xaxis: {
        categories: [
          "Jan",
          "Feb",
          "Mar",
          "Apr",
          "May",
          "Jun",
          "Jul",
          "Aug",
          "Sep",
          "Oct",
          "Nov",
          "Dec",
        ],
        labels: {
          rotate: -90
        },
      },
    };
  (chart = new ApexCharts(
    document.querySelector("#market-overview"),
    options
  )).render();
</script>
