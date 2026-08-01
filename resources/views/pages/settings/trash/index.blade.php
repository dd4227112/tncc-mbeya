<x-page.header />
<x-page.sidebar />

<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="mb-sm-0 font-size-18">Trash</h4>

          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);">Home</a></li>
              <li class="breadcrumb-item active">Trash</li>
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
            {{-- filter section --}}
            <div class="mt-4">
              <form class="row gx-3 gy-2 align-items-center" id="categoryForm" method="POST">
                <div class="col-sm-3">
                  <div class="input-group datepicker-range">
                    <input type="text" placeholder="Select Date Ranges" name="date_range"
                      class="form-control flatpickr-input" data-input aria-describedby="date1">
                  </div>
                </div>
                <div class="col-sm-2">
                  <select class="form-control" data-trigger name="category" id="trash-category"
                    placeholder="Choose status">
                    <option value="">Choose Category</option>
                    <option value="invoices">Invoices</option>
                    <option value="payments">Payments</option>
                    <option value="crops">Crops</option>
                    <option value="units">Units</option>
                    <option value="users">Users</option>

                  </select>
                </div>
                <div class="col-auto">
                  <button type="submit" class="btn btn-primary">View</button>
                </div>
                <div class="col-auto">
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
              </form>
            </div>
            <div id="pageContent">

            </div>

          </div>
          <!-- end card body -->
        </div>
        <!-- end card -->
      </div>
      <!-- end col -->
    </div>
  </div>
</div>
<x-page.footer />
<script>
  $(document).ready(function() {
    $('#categoryForm').on('submit', function(e) {
      e.preventDefault();
      var formData = $(this).serialize();
      $.ajax({
        url: "{{ route('settings.getTrashData') }}",
        type: 'POST',
        dataType: 'html',
        data: formData,
        success: function(response) {
          $('#pageContent').html(response);

          // Destroy any existing instance first (handles repeated submits)
          if ($.fn.DataTable.isDataTable('.global-datatable')) {
            $('.global-datatable').DataTable().destroy();
          }

          // Now initialize on the freshly injected table
          $('.global-datatable').DataTable({
            "order": [
              [0, "asc"]
            ]
          });
        },
        error: function(xhr, status, error) {
          console.log(xhr.responseText); // Log the full response for debugging

          var message = 'Error fetching data';

          try {
            var result = JSON.parse(xhr.responseText);
            if (result && result.message) {
              message = result.message;
            }
          } catch (e) {
            // response wasn't valid JSON, keep the fallback
          }
          toastr.error(message);
        }
      });
    });
    // Handle restore button click
    $('#pageContent').on('click', '.restore-item-btn', function() {
      var itemId = $(this).data('id');
      var model = $(this).data('model');

      Swal.fire({
        title: 'Restore this item?',
        text: 'This item will be restored back to the active list.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, restore it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#34c38f',
        cancelButtonColor: '#f46a6a'
      }).then(function(result) {
        if (!result.isConfirmed) return;

        $.ajax({
          url: "{{ route('settings.restoreItem') }}",
          type: 'POST',
          data: {
            id: itemId,
            model: model,
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            toastr.success(response.message);
            $('#categoryForm').submit(); // Re-submit the form to refresh the data
          },
          error: function(xhr) {
            var message = 'Error restoring item';
            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
              message = xhr.responseJSON.message;
            }
            toastr.error(message);
          }
        });
      });
    });

    // Handle delete button click
    $('#pageContent').on('click', '.delete-item-btn', function() {
      var itemId = $(this).data('id');
      var model = $(this).data('model');

      Swal.fire({
        title: 'Delete this item?',
        text: 'This will permanently delete the item. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f46a6a',
        cancelButtonColor: '#74788d'
      }).then(function(firstResult) {
        if (!firstResult.isConfirmed) return;

        // Second confirmation
        Swal.fire({
          title: 'Are you absolutely sure?',
          text: 'This is your last chance — the item cannot be restored after this.',
          icon: 'error',
          showCancelButton: true,
          confirmButtonText: 'Yes, permanently delete',
          cancelButtonText: 'No, keep it',
          confirmButtonColor: '#f46a6a',
          cancelButtonColor: '#74788d'
        }).then(function(secondResult) {
          if (!secondResult.isConfirmed) return;

          $.ajax({
            url: "{{ route('settings.deleteItem') }}",
            type: 'POST',
            data: {
              id: itemId,
              model: model,
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              toastr.success(response.message);
              $('#categoryForm').submit(); // Re-submit the form to refresh the data
            },
            error: function(xhr) {
              var message = 'Error deleting item';
              if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
              }
              toastr.error(message);
            }
          });
        });
      });
    });
  });
</script>
