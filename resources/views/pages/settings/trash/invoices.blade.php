 <!-- end row -->
 <div class="table-responsive">
   <table id="invoices-table" class="table align-middle datatable dt-responsive table-check nowrap global-datatable"
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
       @forelse ($trashedData as $key=>$invoice)
         <tr>
           <td>{{ ++$key }}</td>
           <td>{{ $invoice->reference_number }}</td>
           <td>{{ optional($invoice->date)->format('d M, Y') }}</td>
           <td>{{ optional($invoice->customer)->name ?? 'N/A' }}</td>
           <td>{{ number_format($invoice->total_amount, 2) }}</td>
           <td>{{ $invoice->status }}</td>
           <td>{{ optional($invoice->creator)->name ?? 'N/A' }}</td>
           <td>
             <button type="button" class="btn btn-sm btn-primary restore-item-btn"
               data-id="{{ $invoice->id }}" data-model="invoices">
               <i class="bx bx-rotate-left"></i> Restore</button>
             <button type="button" class="btn btn-sm btn-danger delete-item-btn"
               data-id="{{ $invoice->id }}" data-model="invoices">
               <i class="bx bx-trash"></i> Delete
             </button>
           </td>
         </tr>
       @empty
         <tr>
           <td colspan="8" class="text-center">No invoices found.</td>
         </tr>
       @endforelse

     </tbody>
   </table>
 </div>
