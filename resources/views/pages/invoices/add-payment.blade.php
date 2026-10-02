  <div class="modal fade add-payment-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12">
              <h5 id="invoiceDetail">Invoice#: </h5>
              <h5 id="memberDetail">Member: </h5>
              <p class="mb-1"><strong>Location:</strong> <span id="paymentInvoiceLocation">—</span></p>
              <p class="mb-1"><strong>Plate Number:</strong> <span id="paymentInvoicePlateNumber">—</span></p>
            </div>
          </div>
          <form id="addPaymentForm" novalidate>
            <div class="row mt-2">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="add-name">Amount</label>
                  <input type="text" id="add-amount" name="amount" readonly class="form-control" />
                  <span class="invalid-feedback d-block" id="add-amount-error"></span>
                </div>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="add-phone_number">Phone</label>
                  <input type="text" id="add-phone_number" name="phone" class="form-control" value="+255" />
                  <span class="invalid-feedback d-block" id="add-phone_number-error"></span>
                </div>
              </div>
            </div>
            {{-- <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="add-network">Network</label>
                  <select id="add-network" name="network" class="form-control">
                    <option value="">Select Network</option>
                    <option value="airtel-money">Airtel Money</option>
                    <option value="m-pesa">M-Pesa</option>
                    <option value="mixx-by-yas">Mixx by Yas</option>
                    <option value="halopesa">HaloPesa</option>
                  </select>
                  <span class="invalid-feedback d-block" id="add-network-error"></span>
                </div>
              </div>
            </div> --}}
            <div class="row">
              <div class="col-12">
                <div class="form-group mb-3">
                  <label for="add-method">Payment Method</label>
                  <select id="add-method" name="method" class="form-control">
                    <option value="">Select Method</option>
                    <option value="cash" selected>Cash</option>
                    {{-- <option value="mobile">Mobile Money</option> --}}
                  </select>
                  <span class="invalid-feedback d-block" id="add-method-error"></span>
                </div>
              </div>
              <input type="hidden" id="add-invoice-id" name="invoice_id" />
            </div>
            @if (hasPermission('payments.create'))
              <div class="form-group float-end">
                <button id="savePaymentBtn" type="submit" class="btn btn-primary float-end"> <i
                    class="mdi mdi-content-save me-1"></i>Save</button>
              </div>
            @endif
          </form>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
