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
        <th style="width: 80px; min-width: 80px;">Action</th>
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
          <td>
            <button type="button" class="btn btn-sm btn-primary restore-item-btn" data-id="{{ $user->id }}" data-model="users">
              <i class="bx bx-rotate-left"></i> Restore
            </button>
            <button type="button" class="btn btn-sm btn-danger delete-item-btn" data-id="{{ $user->id }}" data-model="users">
              <i class="bx bx-trash"></i> Delete
            </button>
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
