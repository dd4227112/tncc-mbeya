<!-- <body data-layout="horizontal"> -->
<x-page.header />
<x-page.sidebar />

<div class="page-content">
  <div class="container-fluid">

    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="mb-sm-0 font-size-18">Roles</h4>

          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
              <li class="breadcrumb-item active">Roles</li>
            </ol>
          </div>

        </div>
      </div>
    </div>
    <!-- end page title -->

    <!-- Page content goes here -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3">
                  @if (hasPermission('settings.update'))
                    <div>
                      <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target=".add-role-modal"><i class="bx bx-plus me-1"></i> Add New</a>
                    </div>
                  @endif
                </div>
                <!-- end row -->

                <div class="table-responsive mb-4">
                  <table id="roles-table" class="table align-middle dt-responsive table-check nowrap"
                    style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
                    <thead>
                      <tr>
                        <th scope="col" style="width: 30px;"></th>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($roles as $key => $role)
                        @php
                          // permission ids this role currently has, for quick lookup
                          $rolePermissionIds = $role->permissions->pluck('id')->toArray();
                        @endphp

                        {{-- main row --}}
                        <tr class="role-row" style="cursor: pointer;" data-bs-toggle="collapse"
                          data-bs-target="#role-permissions-{{ $role->id }}">
                          <td>
                            <i class="ri-arrow-right-s-line toggle-icon" id="icon-{{ $role->id }}"></i>
                          </td>
                          <td>{{ ++$key }}</td>
                          <td>{{ $role->name }}</td>
                          <td>{{ $role->description }}</td>

                        </tr>

                        {{-- collapsible permissions row --}}
                        <tr>
                          <td colspan="4" style="padding: 0; border: none;">
                            <div id="role-permissions-{{ $role->id }}" class="collapse">
                              <div class="p-3 bg-light">
                                <div class="row">
                                  @foreach ($permissions as $permission)
                                    <div class="col-md-4 col-lg-3 mb-2">
                                      <div class="form-check form-switch">
                                        @if (hasPermission('settings.update'))
                                          <input class="form-check-input permission-toggle" type="checkbox"
                                            role="switch" id="perm-{{ $role->id }}-{{ $permission->id }}"
                                            data-role-id="{{ $role->id }}"
                                            data-permission-id="{{ $permission->id }}"
                                            {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}>
                                        @endif
                                        <label class="form-check-label" style="font-size: 1.2em"
                                          for="perm-{{ $role->id }}-{{ $permission->id }}">
                                          {!! ucwords(str_replace('.', ' ', implode('.', array_reverse(explode('.', $permission->name))))) !!}
                                        </label>
                                      </div>
                                    </div>
                                  @endforeach
                                </div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                <!-- end page content -->

              </div> <!-- container-fluid -->
            </div>
          </div>
        </div>
      </div>
      <!-- End Page-content -->
      @include('pages.settings.add')
      @push('scripts')
        @include('pages.settings.scripts.manage-role')
        @include('pages.settings.scripts.permissions')
      @endpush

      <x-page.footer />
