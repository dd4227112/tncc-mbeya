<script>
  $(function() {
    $('#addUserForm').on('submit', function(e) {
      e.preventDefault();

      var $form = $(this);
      var $submitButton = $form.find('button[type="submit"]');
      var $firstNameInput = $form.find('[name="first_name"]');
      var $lastNameInput = $form.find('[name="last_name"]');
      var $phoneInput = $form.find('[name="phone"]');
      var $emailInput = $form.find('[name="email"]');
      var $addressInput = $form.find('[name="address"]');
      var $roleInput = $form.find('[name="role_id"]');
      var firstName = $.trim($firstNameInput.val());
      var lastName = $.trim($lastNameInput.val());
      var phone = $.trim($phoneInput.val());
      var email = $.trim($emailInput.val());
      var address = $.trim($addressInput.val());
      var role = $.trim($roleInput.val());

      $form.find('.is-invalid').removeClass('is-invalid');
      $form.find('.invalid-feedback').text('');

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

      if ($roleInput.length && !role) {
        $roleInput.addClass('is-invalid').focus();
        $('#add-role-error').text('Please select a role.');
        return;
      }

      $submitButton.prop('disabled', true).text('Saving...');

      $.ajax({
        url: '{{ route($role . '.store') }}',
        type: 'POST',
        data: $form.serialize(),
        success: function(response) {
          $form[0].reset();
          $('.add-user-modal').modal('hide');
          toastr.success(response.message || 'User created successfully.');
          if (typeof searchMembers === 'function') {
            searchMembers(firstName);
          } else if (typeof loadUsersTable === 'function') {
            loadUsersTable();
          }
        },
        error: function(xhr) {
          console.error('Error response:', xhr); // Log the entire error response for debugging
          var message = 'Unable to save user. Please try again.';

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
