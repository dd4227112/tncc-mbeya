  <script>
    $(document).ready(function() {

      // rotate arrow icon on expand/collapse
      $('.role-row').on('click', function() {
        const target = $(this).data('bs-target');
        const $icon = $(this).find('.toggle-icon');
        $(target).on('shown.bs.collapse', function() {
          $icon.addClass('rotate-90');
        });
        $(target).on('hidden.bs.collapse', function() {
          $icon.removeClass('rotate-90');
        });
      });

      // stop row click from firing when clicking directly on switches inside the collapse
      $(document).on('click', '.permission-toggle', function(e) {
        e.stopPropagation();
      });

      // AJAX toggle
      $(document).on('change', '.permission-toggle', function() {
        const $checkbox = $(this);
        const roleId = $checkbox.data('role-id');
        const permissionId = $checkbox.data('permission-id');

        $.ajax({
          url: "{{ route('roles.toggle-permission') }}",
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            role_id: roleId,
            permission_id: permissionId
          },
          success: function(res) {
            // keep checkbox state in sync with server response
            $checkbox.prop('checked', res.status);
            toastr.success('Permission ' + (res.status ? 'granted' : 'revoked') + ' successfully.');

          },
          error: function() {
            // revert checkbox on failure
            $checkbox.prop('checked', !$checkbox.prop('checked'));
            toastr.error('Failed to toggle permission. Please try again.');
          }
        });
      });
    });
  </script>

  @push('styles')
    <style>
      .toggle-icon {
        transition: transform 0.2s ease;
        display: inline-block;
      }

      .toggle-icon.rotate-90 {
        transform: rotate(90deg);
      }
    </style>
  @endpush
