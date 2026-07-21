  <script>
    $(function() {

      function clearAddErrors() {
        $('#updateProfileForm').find('.is-invalid').removeClass('is-invalid');
        $('#updateProfileForm').find('.invalid-feedback').text('');
      }

      $('#changePasswordForm').on('submit', function(e) {
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
          success: function(response) {
            $form[0].reset();
            $('.change-password-modal').modal('hide');
            toastr.success(response.message || 'Password updated successfully.');
          },
          error: function(xhr) {
            var message = 'Unable to update password. Please try again.';

            if (xhr && xhr.responseJSON) {
              if (xhr.responseJSON.errors) {
                if (xhr.responseJSON.errors.current_password) {
                  $currentPasswordInput.addClass('is-invalid');
                  $('#update_password_current_password-error').text(xhr.responseJSON.errors
                    .current_password[0]);
                }

                if (xhr.responseJSON.errors.password) {
                  $passwordInput.addClass('is-invalid');
                  $('#update_password_password-error').text(xhr.responseJSON.errors.password[0]);
                }

                if (xhr.responseJSON.errors.password_confirmation) {
                  $passwordConfirmationInput.addClass('is-invalid');
                  $('#update_password_password_confirmation-error').text(xhr.responseJSON.errors
                    .password_confirmation[0]);
                }
              }

              if (xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
              }
            }

            if (!$('#update_password_current_password-error').text() && !$(
                '#update_password_password-error').text() && !$(
                '#update_password_password_confirmation-error').text()) {
              toastr.error(message);
            }
          },
          complete: function() {
            $submitButton.prop('disabled', false).html('<i class="mdi mdi-content-save me-1"></i>Save');
          }
        });
      });


      // get user profile data and populate the form fields
      $('.show-profile').on('click', function(e) {
        e.preventDefault();
        $.ajax({
          url: '{{ route('profile.edit') }}',
          type: 'GET',
          success: function(response) {
            if (response.user) {
              $('#add-first-name').val(response.user.first_name);
              $('#add-last-name').val(response.user.last_name);
              $('#add-phone').val(response.user.phone);
              $('#add-email').val(response.user.email);
              $('#add-address').val(response.user.address);
              $('#add-role').val(response.user.role);
              //show the modal after populating the fields
              $('.profile-modal').modal('show');
            }
          },
          error: function() {
            toastr.error('Unable to fetch user profile data. Please try again.');
          }
        });
      });

      // update profile
      $('#updateProfileForm').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $submitButton = $form.find('button[type="submit"]');
        var $firstNameInput = $('#add-first-name');
        var $lastNameInput = $('#add-last-name');
        var $phoneInput = $('#add-phone');
        var $emailInput = $('#add-email');
        var $addressInput = $('#add-address');
        var $roleInput = $('#add-role');
        var firstName = $.trim($firstNameInput.val());
        var lastName = $.trim($lastNameInput.val());
        var phone = $.trim($phoneInput.val());
        var email = $.trim($emailInput.val());
        var address = $.trim($addressInput.val());

        clearAddErrors();

        if (!firstName) {
          $firstNameInput.addClass('is-invalid').focus();
          $('#add-first-name-error').text('Please enter the first name.');
          return;
        }

        if (!lastName) {
          $lastNameInput.addClass('is-invalid').focus();
          $('#add-last-name-error').text('Please enter the last name.');
          return;
        }

        if (!phone) {
          $phoneInput.addClass('is-invalid').focus();
          $('#add-phone-error').text('Please enter the phone number.');
          return;
        }

        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          $emailInput.addClass('is-invalid').focus();
          $('#add-email-error').text('Please enter a valid email.');
          return;
        }


        $submitButton.prop('disabled', true).text('Updating...');
        $.ajax({
          url: "{{ route('profile.update') }}",
          type: 'PATCH',
          data: $form.serialize(),
          success: function(response) {
            $('.show-profile').modal('hide');
            toastr.success(response.message || 'User updated successfully.');
            setTimeout(() => {
              window.location.reload();
            }, 700);
          },
          error: function(xhr) {
            var message = 'Unable to update user. Please try again.';

            if (xhr && xhr.responseJSON) {
              if (xhr.responseJSON.errors) {
                if (xhr.responseJSON.errors.first_name) {
                  $firstNameInput.addClass('is-invalid');
                  $('#add-first-name-error').text(xhr.responseJSON.errors.first_name[0]);
                }
                if (xhr.responseJSON.errors.last_name) {
                  $lastNameInput.addClass('is-invalid');
                  $('#add-last-name-error').text(xhr.responseJSON.errors.last_name[0]);
                }
                if (xhr.responseJSON.errors.phone) {
                  $phoneInput.addClass('is-invalid');
                  $('#add-phone-error').text(xhr.responseJSON.errors.phone[0]);
                }
                if (xhr.responseJSON.errors.email) {
                  $emailInput.addClass('is-invalid');
                  $('#add-email-error').text(xhr.responseJSON.errors.email[0]);
                }
                if (xhr.responseJSON.errors.address) {
                  $addressInput.addClass('is-invalid');
                  $('#add-address-error').text(xhr.responseJSON.errors.address[0]);
                }
                if (xhr.responseJSON.errors.role) {
                  $roleInput.addClass('is-invalid');
                  $('#add-role-error').text(xhr.responseJSON.errors.role[0]);
                }
              }
              if (xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
              }
            }

            if (!$('#add-first-name-error').text() && !$('#add-last-name-error').text() && !$(
                '#add-phone-error').text() && !$('#add-email-error').text() && !$('#add-address-error')
              .text() && !$('#add-role-error').text()) {
              toastr.error(message);
            }
          },
          complete: function() {
            $submitButton.prop('disabled', false).text('Save');
          }
        });
      });
    });
  </script>
