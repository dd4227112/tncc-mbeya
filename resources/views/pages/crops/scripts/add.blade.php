<script>
  $(function() {
    $('#addCropForm').on('submit', function(e) {
      e.preventDefault();

      var $form = $(this);
      var $submitButton = $form.find('button[type="submit"]');
      var $nameInput = $form.find('[name="name"]');
      var $descriptionInput = $form.find('[name="description"]');
      var $priceInput = $form.find('[name="price"]');
      var $unitInput = $form.find('[name="unit_id"]');
      var name = $.trim($nameInput.val());
      var description = $.trim($descriptionInput.val());
      var price = $.trim($priceInput.val());
      var unit_id = $.trim($unitInput.val());

      $form.find('.is-invalid').removeClass('is-invalid');
      $form.find('.invalid-feedback').text('');

      if (!name) {
        $nameInput.addClass('is-invalid').focus();
        $('#add-name-error').text('Please enter the crop name.');
        return;
      }

      // if (!description) {
      //     $descriptionInput.addClass('is-invalid').focus();
      //     $('#add-description-error').text('Please enter the crop description.');
      //     return;
      // }

      if (!price) {
        $priceInput.addClass('is-invalid').focus();
        $('#add-price-error').text('Please enter the crop price.');
        return;
      }

      if (!unit_id) {
        $unitInput.addClass('is-invalid').focus();
        $('#add-unit-error').text('Please select a unit.');
        return;
      }

      $submitButton.prop('disabled', true).text('Saving...');

      $.ajax({
        url: '{{ route('crops.store') }}',
        type: 'POST',
        data: $form.serialize(),
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $form[0].reset();
          $('.add-crop-modal').modal('hide');
          toastr.success(response.message || 'Crop created successfully.');
          if (typeof searchCrops === 'function') {
            searchCrops(name);
          } else if (typeof loadCropsTable === 'function') {
            loadCropsTable();
          }
        },
        error: function(xhr) {
          var message = 'Unable to save crop. Please try again.';

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
              if (xhr.responseJSON.errors.price) {
                $priceInput.addClass('is-invalid');
                $('#add-price-error').text(xhr.responseJSON.errors.price[0]);
              }
              if (xhr.responseJSON.errors.unit_id) {
                $unitInput.addClass('is-invalid');
                $('#add-unit-error').text(xhr.responseJSON.errors.unit_id[0]);
              }
            }

            if (xhr.responseJSON.message) {
              message = xhr.responseJSON.message;
            }
          }

          if (!$('#add-name-error').text() && !$('#add-price-error').text() && !$(
              '#add-description-error').text() && !$('#add-unit-error').text()) {
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
