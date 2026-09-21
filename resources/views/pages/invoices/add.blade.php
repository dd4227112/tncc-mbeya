  <div class="modal fade add-invoice-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create New Invoice</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">


          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">

                  <!-- Two-column layout: Member | Crops -->
                  <div class="row">

                    <!-- Column 1: Member -->
                    <div class="col-lg-3 mb-4 mb-lg-0">
                      <div class="section-label">1. Select Member</div>
                      <div class="search-box position-relative mb-3">
                        <input type="text" id="memberSearchInput" class="form-control"
                          placeholder="Search member by name or phone…" autocomplete="off">
                        <div id="memberResults" class="search-results"></div>
                      </div>

                      <div id="memberCard" class="member-card">
                        <div class="member-placeholder" id="memberPlaceholder">
                          No member selected yet — search and pick a member.
                        </div>
                        <div id="memberDetails" class="d-none">
                          <div class="d-flex align-items-start">
                            <div class="flex-grow-1 selectedMemberDetails">
                              
                            </div>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0" id="clearMemberBtn"
                              title="Remove member">
                              <i class="mdi mdi-close"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Column 2: Crops -->
                    <div class="col-lg-9">
                      <div class="section-label">2. Add Crop</div>
                      <div class="search-box position-relative mb-3">
                        <input type="text" id="cropSearchInput" class="form-control"
                          placeholder="Search crop by name…" autocomplete="off">
                        <div id="cropResults" class="search-results"></div>
                      </div>

                      <div class="border rounded p-3">
                        <div class="table-responsive">
                          <table class="table table-nowrap align-middle mb-0 crop-table">
                            <thead>
                              <tr>
                                <th style="width: 40px;">No.</th>
                                <th>Crop</th>
                                <th class="text-end" style="width: 90px;">Price (TZS)</th>
                                <th style="width: 70px;">Unit</th>
                                <th style="width: 110px;">Qty</th>
                                <th class="text-end" style="width: 100px;">Amount</th>
                                <th class="text-center" style="width: 40px;"></th>
                              </tr>
                            </thead>
                            <tbody id="cropTableBody">
                              <tr class="empty-row" id="emptyRow">
                                <td colspan="7">No crops added yet. Use the search above to add crops to this
                                  invoice.</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>

                  </div>
                  <!-- end two-column layout -->

                  <!-- Summary -->
                  <div class="row justify-content-end mt-4">
                    <div class="col-lg-5 col-md-7">
                      <div class="py-2">
                        <h5 class="font-size-15">3. Summary</h5>
                      </div>
                      <div class="p-3 p-md-4 border rounded">
                        <table class="table table-borderless summary-table mb-0">
                          <tbody>
                            <tr>
                              <td>Items</td>
                              <td class="text-end" id="summaryItemCount">0</td>
                            </tr>
                            <tr>
                              <td>Sub Total (TZS)</td>
                              <td class="text-end" id="summarySubTotal">$0.00</td>
                            </tr>
                            <tr class="total-row">
                              <td>Total (TZS)</td>
                              <td class="text-end" id="summaryTotal">$0.00</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="mt-4 text-end">
                    <button type="button" class="btn btn-soft-danger waves-effect me-1" data-bs-dismiss="modal"
                      aria-label="Close">Cancel</button>
                    @if (hasPermission('invoices.create'))
                      <button type="button" class="btn btn-primary waves-effect waves-light" id="saveInvoiceBtn">
                        <i class="mdi mdi-content-save me-1"></i>Save
                      </button>
                    @endif
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
