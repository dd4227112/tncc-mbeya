 <div class="table-responsive">
   <table id="units-table" class="table align-middle dt-responsive table-check nowrap global-datatable"
     style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
     <thead>
       <tr>
         <th scope="col">#</th>
         <th scope="col">Name</th>
         <th scope="col">Abbreviation</th>
         <th scope="col">Deleted At</th>
         <th style="width: 200px; min-width: 200px;">Action</th>
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
             <div class="d-flex flex-wrap gap-1">
               <button type="button" class="btn btn-sm btn-soft-success restore-item-btn" data-id="{{ $unit->id }}" data-model="units">
                 <i class="bx bx-rotate-left me-1"></i>Restore</button>
               <button type="button" class="btn btn-sm btn-soft-danger delete-item-btn" data-id="{{ $unit->id }}" data-model="units">
                 <i class="bx bx-trash me-1"></i>Delete</button>
             </div>
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
