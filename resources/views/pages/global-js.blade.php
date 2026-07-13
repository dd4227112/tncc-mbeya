  <script>
    $(function () {
      $('#changePasswordForm').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $submitButton = $form.find('button[type="submit"]');
        var $currentPasswordInput = $form.find('[name="current_password"]');
        var $passwordInput = $form.find('[name="password"]');
        var $passwordConfirmationInput = $form.find('[name="password_confirmation"]');

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');

        $submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i>Saving...');

        $.ajax({
          url: '{{ route('password.update') }}',
          type: 'POST',
          data: $form.serialize(),
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            $form[0].reset();
            $('.change-password-modal').modal('hide');
            toastr.success(response.message || 'Password updated successfully.');
          },
          error: function (xhr) {
            var message = 'Unable to update password. Please try again.';

            if (xhr && xhr.responseJSON) {
              if (xhr.responseJSON.errors) {
                if (xhr.responseJSON.errors.current_password) {
                  $currentPasswordInput.addClass('is-invalid');
                  $('#update_password_current_password-error').text(xhr.responseJSON.errors.current_password[0]);
                }

                if (xhr.responseJSON.errors.password) {
                  $passwordInput.addClass('is-invalid');
                  $('#update_password_password-error').text(xhr.responseJSON.errors.password[0]);
                }

                if (xhr.responseJSON.errors.password_confirmation) {
                  $passwordConfirmationInput.addClass('is-invalid');
                  $('#update_password_password_confirmation-error').text(xhr.responseJSON.errors.password_confirmation[0]);
                }
              }

              if (xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
              }
            }

            if (!$('#update_password_current_password-error').text() && !$('#update_password_password-error').text() && !$('#update_password_password_confirmation-error').text()) {
              toastr.error(message);
            }
          },
          complete: function () {
            $submitButton.prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i>Save');
          }
        });
      });
    });
  </script>