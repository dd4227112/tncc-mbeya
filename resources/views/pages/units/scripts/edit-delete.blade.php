<script>
    $(function () {
        function clearEditErrors() {
            $('#editUnitForm').find('.is-invalid').removeClass('is-invalid');
            $('#editUnitForm').find('.invalid-feedback').text('');
        }

        $(document).on('click', '.edit-unit', function (e) {
            e.preventDefault();

            var unitId = $(this).data('id');
            var $modal = $('.edit-unit-modal');
            var $form = $('#editUnitForm');

            clearEditErrors();
            $form.trigger('reset');
            $form.find('button[type="submit"]').prop('disabled', false).text('Save');

            $.ajax({
                url: "{{ route('units.edit', ['unit' => ':id']) }}".replace(':id', unitId),
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response && response.data) {
                        $('#edit-unit-id').val(response.data.id);
                        $('#edit-name').val(response.data.name);
                        $('#edit-abbreviation').val(response.data.abbreviation);
                        $modal.modal('show');
                    }
                },
                error: function () {
                    toastr.error('Unable to load unit details. Please try again.');
                }
            });
        });

        $('#editUnitForm').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var unitId = $('#edit-unit-id').val();
            var $submitButton = $form.find('button[type="submit"]');
            var $nameInput = $('#edit-name');
            var $abbreviationInput = $('#edit-abbreviation');
            var name = $.trim($nameInput.val());
            var abbreviation = $.trim($abbreviationInput.val());

            clearEditErrors();

            if (!name) {
                $nameInput.addClass('is-invalid').focus();
                $('#edit-name-error').text('Please enter the unit name.');
                return;
            }

            if (!abbreviation) {
                $abbreviationInput.addClass('is-invalid').focus();
                $('#edit-abbreviation-error').text('Please enter the abbreviation.');
                return;
            }

            $submitButton.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('units.update', ['unit' => ':id']) }}".replace(':id', unitId),
                type: 'PUT',
                data: $form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('.edit-unit-modal').modal('hide');
                    toastr.success(response.message || 'Unit updated successfully.');
                    if (typeof loadUnitsTable === 'function') {
                        loadUnitsTable();
                    }
                },
                error: function (xhr) {
                    var message = 'Unable to update unit. Please try again.';

                    if (xhr && xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            if (xhr.responseJSON.errors.name) {
                                $nameInput.addClass('is-invalid');
                                $('#edit-name-error').text(xhr.responseJSON.errors.name[0]);
                            }
                            if (xhr.responseJSON.errors.abbreviation) {
                                $abbreviationInput.addClass('is-invalid');
                                $('#edit-abbreviation-error').text(xhr.responseJSON.errors.abbreviation[0]);
                            }
                        }
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                    }

                    if (!$('#edit-name-error').text() && !$('#edit-abbreviation-error').text()) {
                        toastr.error(message);
                    }
                },
                complete: function () {
                    $submitButton.prop('disabled', false).text('Save');
                }
            });
        });

        $(document).on('click', '.delete-unit', function (e) {
            e.preventDefault();

            var unitId = $(this).data('id');

            Swal.fire({
                title: 'Delete Unit',
                text: 'Are you sure you want to delete this unit? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('units.destroy', ['unit' => ':id']) }}".replace(':id', unitId),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (typeof loadUnitsTable === 'function') {
                                loadUnitsTable();
                            }
                            Swal.fire('Deleted!', response.message || 'Unit deleted successfully.', 'success');
                        },
                        error: function (xhr) {
                            var message = 'Unable to delete unit. Please try again.';
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
