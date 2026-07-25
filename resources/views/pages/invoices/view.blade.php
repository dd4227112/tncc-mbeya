  <div class="modal fade invoice-detail-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 py-2">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-3">
          <div class="row justify-content-center">
            <div class="col-12">
              <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                  <div class="invoice-title text-center mb-3">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                      <img src="{{ asset('assets/images/tncc-logo.png') }}" alt="TNCC Mbeya" height="42"
                        class="mb-2">
                      <span class="logo-txt fs-5 fw-semibold">TNCC- Mbeya</span>
                    </div>
                    <div class="mt-2 text-muted small">
                      <p class="mb-1" id="">Kasumulu Border-Mbeya</p>
                      <p class="mb-1" id=""><i class="mdi mdi-email align-middle me-1"></i>
                        josephatibenjamini13@gmail.com</p>
                      <p class="mb-0" id=""><i class="mdi mdi-phone align-middle me-1"></i>
                        +255 747 814 565</p>
                    </div>
                  </div>

                  <div class="text-center mb-3">
                    <h5 class="mb-1 fw-semibold" id="invoiceDetailNumber">Invoice # 12345</h5>
                    <p class="text-muted small mb-0">Invoice Date: <span id="invoiceDetailOrderDate">February 16,
                        2020</span></p>
                  </div>
                  <hr class="my-3">
                  <div class="row gx-3 gy-3">
                    <div class="col-md-6">
                      <div class="border rounded-3 p-3 h-100 bg-light">
                        <p class="text-uppercase text-muted small mb-2">Billed To</p>
                        <h6 class="mb-1 fw-semibold" id="invoiceDetailBilledName">Richard Saul</h6>
                        <p class="small mb-1" id="invoiceDetailBilledAddress">1208 Sherwood Circle Lafayette, LA 70506
                        </p>
                        <p class="small mb-1" id="invoiceDetailBilledEmail">RichardSaul@rhyta.com</p>
                        <p class="small mb-0" id="invoiceDetailBilledPhone">337-256-9134</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="border rounded-3 p-3 h-100 bg-light">
                        <p class="text-uppercase text-muted small mb-2">Invoice info</p>
                        <p class="small mb-1"><strong>Status:</strong> <span id="invoiceDetailStatus">Pending</span></p>
                        <p class="small mb-1"><strong>Date:</strong> <span id="invoiceDetailOrderDate">February 16,
                            2020</span></p>
                        <p class="small mb-1"><strong>Payment Status:</strong> <span
                            id="invoiceDetailPaymentStatus">-</span></p>
                        <p class="small mb-1"><strong>Payment Reference:</strong> <span
                            id="invoiceDetailPaymentReference">reference</span></p>
                        <p class="small mb-1"><strong>Payment Method:</strong> <span
                            id="invoiceDetailPaymentMethod">-</span></p>
                      </div>
                    </div>
                  </div>

                  <div class="py-2 mt-3">
                    <h6 class="mb-2 fw-semibold">Invoice Items</h6>
                  </div>
                  <div class="p-4 border rounded">
                    <div class="table-responsive">
                      <table class="table table-nowrap align-middle mb-0">
                        <thead>
                          <tr>
                            <th style="width: 70px;">No.</th>
                            <th>Item</th>
                            <th class="text-end" style="width: 120px;">Price (TZS)</th>
                          </tr>
                        </thead>
                        <tbody id="invoiceDetailItemsBody"></tbody>
                        <tfoot>
                          <tr>
                            <th scope="row" colspan="2" class="text-end">Sub Total (TZS)</th>
                            <td class="text-end" id="invoiceDetailSubTotal"> 0.00</td>
                          </tr>
                          <tr>
                            <th scope="row" colspan="2" class="border-0 text-end">Total (TZS)</th>
                            <td class="border-0 text-end">
                              <h5 class="m-0" id="invoiceDetailTotal"> 0.00</h5>
                            </td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="d-print-none mt-3">
                    <div class="float-end">
                      <a href="#" class="btn btn-soft-danger me-1 waves-effect waves-light"
                        data-bs-dismiss="modal">Cancel</a>
                         @if (hasPermission('invoices.print') )
                      <a href="javascript:void(0)" onclick="printInvoiceDetailModal()"
                        class="btn btn-success waves-effect waves-light w-md ">Print</a>
                        @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
