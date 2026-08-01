  <div class="table-responsive mb-4">
    <table id="crops-table" class="table align-middle dt-responsive table-check nowrap global-datatable"
      style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Name</th>
          <th scope="col">Description</th>
          <th scope="col">Unit</th>
          <th scope="col">Price</th>
          <th scope="col">Deleted At</th>
          <th style="width: 80px; min-width: 80px;">Action</th>
        </tr>
      </thead>
      <tbody id="crops-table-body">
        @forelse ($trashedData as $crop)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $crop->name }}</td>
            <td>{{ $crop->description }}</td>
            <td>{{ $crop->unit->name ?? 'N/A' }}</td>
            <td>{{ $crop->price }}</td>
            <td>{{ $crop->deleted_at }}</td>
            <td>
              <button type="button" class="btn btn-sm btn-primary restore-item-btn" data-id="{{ $crop->id }}"
                data-model="crops">
                <i class="bx bx-rotate-left"></i> Restore</button>
              <button type="button" class="btn btn-sm btn-danger delete-item-btn" data-id="{{ $crop->id }}"
                data-model="crops">
                <i class="bx bx-trash"></i> Delete
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center">No crops found.</td>
          </tr>
        @endforelse

      </tbody>
    </table>
    <!-- end table -->
  </div>
