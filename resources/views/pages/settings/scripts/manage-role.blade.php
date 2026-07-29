<script>
  $(function() {
    $('#addroleForm').on('submit', function(e) {
      e.preventDefault();

      var $form = $(this);
      var $submitButton = $form.find('button[type="submit"]');
      var $nameInput = $form.find('[name="name"]');
      var $descriptionInput = $form.find('[name="description"]');
      var name = $.trim($nameInput.val());
      var description = $.trim($descriptionInput.val());

      $form.find('.is-invalid').removeClass('is-invalid');
      $form.find('.invalid-feedback').text('');

      if (!name) {
        $nameInput.addClass('is-invalid').focus();
        $('#add-name-error').text('Please enter the role name.');
        return;
      }

      if (!description) {
        $descriptionInput.addClass('is-invalid').focus();
        $('#add-description-error').text('Please enter the description.');
        return;
      }

      $submitButton.prop('disabled', true).text('Saving...');

      $.ajax({
        url: '{{ route('roles.store') }}',
        type: 'POST',
        data: $form.serialize(),
        success: function(response) {
          $form[0].reset();
          $('.add-role-modal').modal('hide');
          toastr.success(response.message || 'role created successfully.');
          window.location.reload();
        },
        error: function(xhr) {
          var message = 'Unable to save role. Please try again.';

          if (xhr && xhr.responseJSON) {
            if (xhr.responseJSON.errors) {
              if (xhr.responseJSON.errors.name) {
                $nameInput.addClass('is-invalid');
                $('#add-name-error').text(xhr.responseJSON.errors.name[0]);
              }

              if (xhr.responseJSON.errors.description) {
                $descriptionInput.addClass('is-invalid');
                $('#add-description-error').text(xhr.responseJSON.errors.description[0]);
              }
            }

            if (xhr.responseJSON.message) {
              message = xhr.responseJSON.message;
            }
          }

          if (!$('#add-name-error').text() && !$('#add-description-error').text()) {
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
