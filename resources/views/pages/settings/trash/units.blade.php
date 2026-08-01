 <div class="table-responsive">
   <table id="units-table" class="table align-middle dt-responsive table-check nowrap global-datatable"
     style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
     <thead>
       <tr>
         <th scope="col">#</th>
         <th scope="col">Name</th>
         <th scope="col">Abbreviation</th>
         <th scope="col">Deleted At</th>
         <th style="width: 80px; min-width: 80px;">Action</th>
       </tr>
     </thead>
     <tbody id="units-table-body">
       @forelse ($trashedData as $unit)
         <tr>
           <td>{{ $unit->id }}</td>
           <td>{{ $unit->name }}</td>
           <td>{{ $unit->abbreviation }}</td>
           <td>{{ $unit->deleted_at }}</td>
           <td>
             <button type="button" class="btn btn-sm btn-primary restore-item-btn" data-id="{{ $unit->id }}" data-model="units">
               <i class="bx bx-rotate-left"></i> Restore</button>
             <button type="button" class="btn btn-sm btn-danger delete-item-btn" data-id="{{ $unit->id }}" data-model="units">
               <i class="bx bx-trash"></i> Delete
             </button>
           </td>
         </tr>
       @empty
         <tr>
           <td colspan="4" class="text-center">No units found.</td>
         </tr>
       @endforelse
     </tbody>
   </table>
   <!-- end table -->
 </div>
