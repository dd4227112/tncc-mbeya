<div class="table-responsive">
  <table id="users-table" class="table align-middle dt-responsive table-check nowrap global-datatable"
    style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">First Name</th>
        <th scope="col">Last Name</th>
        <th scope="col">Phone</th>
        <th scope="col">Email</th>
        <th scope="col">Address</th>
        <th scope="col">Roles</th>
        <th scope="col">Deleted At</th>

        <th style="width: 200px; min-width: 200px;">Action</th>
      </tr>
    </thead>
    <tbody id="users-table-body">
      @forelse ($trashedData as $user)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $user->first_name }}</td>
          <td>{{ $user->last_name }}</td>
          <td>{{ $user->phone }}</td>
          <td>{{ $user->email }}</td>
          <td>{{ $user->address }}</td>
          <td>{{ implode(', ', $user->roles->pluck('name')->toArray()) }}</td>
          <td>{{ $user->deleted_at }}</td>
          <td>
            <div class="d-flex flex-wrap gap-1">
              <button type="button" class="btn btn-sm btn-soft-success restore-item-btn" data-id="{{ $user->id }}"
                data-model="users"><i class="bx bx-rotate-left me-1"></i>Restore</button>
              <button type="button" class="btn btn-sm btn-soft-danger delete-item-btn" data-id="{{ $user->id }}"
                data-model="users"><i class="bx bx-trash me-1"></i>Delete</button>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center">No users found.</td>
        </tr>
      @endforelse

    </tbody>
  </table>
  <!-- end table -->
</div>
