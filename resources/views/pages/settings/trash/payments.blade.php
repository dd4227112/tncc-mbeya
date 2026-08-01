  <div class="table-responsive">
    <table id="payments-table" class="table align-middle datatable dt-responsive table-check nowrap global-datatable"
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
          <th style="width: 90px;">Action</th>
        </tr>
      </thead>
      <tbody>
        <!-- Payments data will be populated here via AJAX -->
        @forelse ($trashedData as $payment)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ optional($payment->date)->format('d M, Y') }}</td>
            <td>{{ optional($payment->payer)->name ?? 'N/A' }}</td>
            <td>{{ number_format($payment->amount, 2) }}</td>
            <td>{{ $payment->transaction_reference }}</td>
            <td>{{ optional($payment->invoice)->reference_number }}</td>
            <td>{{ $payment->payment_method }}</td>
            <td>{{ $payment->status }}</td>
            <td>{{ optional($payment->receiver)->name ?? 'N/A' }}</td>
            <td>
              <button type="button" class="btn btn-sm btn-primary restore-item-btn"
                data-id="{{ $payment->id }}" data-model="payments">
                <i class="bx bx-rotate-left"></i> Restore
              </button>
              <button type="button" class="btn btn-sm btn-danger delete-item-btn"
                data-id="{{ $payment->id }}" data-model="payments">
                <i class="bx bx-trash"></i> Delete
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="text-center">No payments found.</td>
          </tr>
        @endforelse

      </tbody>
    </table>
  </div>
