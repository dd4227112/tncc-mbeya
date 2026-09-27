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
          <th style="width: 200px; min-width: 200px;">Action</th>
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
              <div class="d-flex flex-wrap gap-1">
                <button type="button" class="btn btn-sm btn-soft-success restore-item-btn" data-id="{{ $crop->id }}"
                  data-model="crops"><i class="bx bx-rotate-left me-1"></i>Restore</button>
                <button type="button" class="btn btn-sm btn-soft-danger delete-item-btn" data-id="{{ $crop->id }}"
                  data-model="crops"><i class="bx bx-trash me-1"></i>Delete</button>
              </div>
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
