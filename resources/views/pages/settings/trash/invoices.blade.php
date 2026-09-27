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
         <th scope="col">Deleted At</th>

         <th style="width: 200px; min-width: 200px;">Action</th>
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
            <td>{{ $invoice->deleted_at }}</td>
           <td>
             <div class="d-flex flex-wrap gap-1">
               <button type="button" class="btn btn-sm btn-soft-success restore-item-btn" data-id="{{ $invoice->id }}"
                 data-model="invoices"><i class="bx bx-rotate-left me-1"></i>Restore</button>
               <button type="button" class="btn btn-sm btn-soft-danger delete-item-btn" data-id="{{ $invoice->id }}"
                 data-model="invoices"><i class="bx bx-trash me-1"></i>Delete</button>
             </div>
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
