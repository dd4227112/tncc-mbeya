<script>
    $(function () {
        $('#addUnitForm').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submitButton = $form.find('button[type="submit"]');
            var $nameInput = $form.find('[name="name"]');
            var $abbreviationInput = $form.find('[name="abbreviation"]');
            var name = $.trim($nameInput.val());
            var abbreviation = $.trim($abbreviationInput.val());

            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('');

            if (!name) {
                $nameInput.addClass('is-invalid').focus();
                $('#add-name-error').text('Please enter the unit name.');
                return;
            }

            if (!abbreviation) {
                $abbreviationInput.addClass('is-invalid').focus();
                $('#add-abbreviation-error').text('Please enter the abbreviation.');
                return;
            }

            $submitButton.prop('disabled', true).text('Saving...');

            $.ajax({
                url: '{{ route('units.store') }}',
                type: 'POST',
                data: $form.serialize(),
                success: function (response) {
                    $form[0].reset();
                    $('.add-unit-modal').modal('hide');
                    toastr.success(response.message || 'Unit created successfully.');
                    if (typeof loadUnitsTable === 'function') {
                        loadUnitsTable();
                    }
                },
                error: function (xhr) {
                    var message = 'Unable to save unit. Please try again.';

                    if (xhr && xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            if (xhr.responseJSON.errors.name) {
                                $nameInput.addClass('is-invalid');
                                $('#add-name-error').text(xhr.responseJSON.errors.name[0]);
                            }

                            if (xhr.responseJSON.errors.abbreviation) {
                                $abbreviationInput.addClass('is-invalid');
                                $('#add-abbreviation-error').text(xhr.responseJSON.errors.abbreviation[0]);
                            }
                        }

                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                    }

                    if (!$('#add-name-error').text() && !$('#add-abbreviation-error').text()) {
                        toastr.error(message);
                    }
                },
                complete: function () {
                    $submitButton.prop('disabled', false).text('Save');
                }
            });
        });
    });
</script>
