<script>
    $(function () {
        function clearEditErrors() {
            $('#editCropForm').find('.is-invalid').removeClass('is-invalid');
            $('#editCropForm').find('.invalid-feedback').text('');
        }

        $(document).on('click', '.edit-crop', function (e) {
            e.preventDefault();

            var cropId = $(this).data('id');
            var $modal = $('.edit-crop-modal');
            var $form = $('#editCropForm');

            clearEditErrors();
            $form.trigger('reset');
            $form.find('button[type="submit"]').prop('disabled', false).text('Save');

            $.ajax({
                url: "{{ route('crops.edit', ['crop' => ':id']) }}".replace(':id', cropId),
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response && response.data) {
                        $('#edit-crop-id').val(response.data.id);
                        $('#edit-name').val(response.data.name);
                        $('#edit-description').val(response.data.description);
                        $('#edit-price').val(response.data.price);
                        $('#edit-unit').val(response.data.unit_id);
                        $modal.modal('show');
                    }
                },
                error: function () {
                    toastr.error('Unable to load crop details. Please try again.');
                }
            });
        });

        $('#editCropForm').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var cropId = $('#edit-crop-id').val();
            var $submitButton = $form.find('button[type="submit"]');
            var $nameInput = $('#edit-name');
            var $descriptionInput = $('#edit-description');
            var $priceInput = $('#edit-price');
            var $unitInput = $('#edit-unit');
            var name = $.trim($nameInput.val());
            var description = $.trim($descriptionInput.val());
            var price = $.trim($priceInput.val());
            var unitId = $.trim($unitInput.val());

            clearEditErrors();

            if (!name) {
                $nameInput.addClass('is-invalid').focus();
                $('#edit-name-error').text('Please enter the crop name.');
                return;
            }

            // if (!description) {
            //     $descriptionInput.addClass('is-invalid').focus();
            //     $('#edit-description-error').text('Please enter the description.');
            //     return;
            // }

            if (!price) {
                $priceInput.addClass('is-invalid').focus();
                $('#edit-price-error').text('Please enter the price.');
                return;
            }
            if (!unitId) {
                $unitInput.addClass('is-invalid').focus();
                $('#edit-unit-error').text('Please select a unit.');
                return;
            }
            $submitButton.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('crops.update', ['crop' => ':id']) }}".replace(':id', cropId),
                type: 'PUT',
                data: $form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('.edit-crop-modal').modal('hide');
                    toastr.success(response.message || 'Crop updated successfully.');
                    if (typeof loadCropsTable === 'function') {
                        loadCropsTable();
                    }
                },
                error: function (xhr) {
                    var message = 'Unable to update crop. Please try again.';

                    if (xhr && xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            if (xhr.responseJSON.errors.name) {
                                $nameInput.addClass('is-invalid');
                                $('#edit-name-error').text(xhr.responseJSON.errors.name[0]);
                            }
                            if (xhr.responseJSON.errors.description) {
                                $descriptionInput.addClass('is-invalid');
                                $('#edit-description-error').text(xhr.responseJSON.errors.description[0]);
                            }
                            if (xhr.responseJSON.errors.price) {
                                $priceInput.addClass('is-invalid');
                                $('#edit-price-error').text(xhr.responseJSON.errors.price[0]);
                            }
                            if (xhr.responseJSON.errors.unit_id) {
                                $unitInput.addClass('is-invalid');
                                $('#edit-unit-error').text(xhr.responseJSON.errors.unit_id[0]);
                            }
                        }
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                    }

                    if (!$('#edit-name-error').text() && !$('#edit-description-error').text() && !$('#edit-price-error').text() && !$('#edit-unit-error').text()) {
                        toastr.error(message);
                    }
                },
                complete: function () {
                    $submitButton.prop('disabled', false).text('Save');
                }
            });
        });

        $(document).on('click', '.delete-crop', function (e) {
            e.preventDefault();

            var cropId = $(this).data('id');

            Swal.fire({
                title: 'Delete Crop',
                text: 'Are you sure you want to delete this crop? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('crops.destroy', ['crop' => ':id']) }}".replace(':id', cropId),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (typeof loadCropsTable === 'function') {
                                loadCropsTable();
                            }
                            Swal.fire('Deleted!', response.message || 'Crop deleted successfully.', 'success');
                        },
                        error: function (xhr) {
                            var message = 'Unable to delete crop. Please try again.';
                            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', message, 'error');
                        }
                    });
                }
            });
        });
    });
</script>
