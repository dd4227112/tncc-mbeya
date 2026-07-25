<script>
    $(function () {
        function clearEditErrors() {
            $('#editUserForm').find('.is-invalid').removeClass('is-invalid');
            $('#editUserForm').find('.invalid-feedback').text('');
        }

        $(document).on('click', '.edit-user', function (e) {
            e.preventDefault();

            var userId = $(this).data('id');
            var $modal = $('.edit-user-modal');
            var $form = $('#editUserForm');

            clearEditErrors();
            $form.trigger('reset');
            $form.find('button[type="submit"]').prop('disabled', false).text('Save');

            $.ajax({
                url: "{{ route($role . '.edit', [$role === 'members' ? 'member' : 'staff' => ':id']) }}".replace(':id', userId),
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response && response.data) {
                        $('#edit-user-id').val(response.data.id);
                        $('#edit-first-name').val(response.data.first_name);
                        $('#edit-last-name').val(response.data.last_name);
                        $('#edit-phone').val(response.data.phone);
                        $('#edit-email').val(response.data.email);
                        $('#edit-address').val(response.data.address);
                        if ($('#edit-role').length) {
                            $('#edit-role').val(response.data.role_id);
                        }
                        $modal.modal('show');
                    }
                },
                error: function (error) {
                    toastr.error(error.responseJSON && error.responseJSON.message ? error.responseJSON.message : 'Unable to fetch user details. Please try again.');
                }
            });
        });

        $('#editUserForm').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var userId = $('#edit-user-id').val();
            var $submitButton = $form.find('button[type="submit"]');
            var $firstNameInput = $('#edit-first-name');
            var $lastNameInput = $('#edit-last-name');
            var $phoneInput = $('#edit-phone');
            var $emailInput = $('#edit-email');
            var $addressInput = $('#edit-address');
            var $roleInput = $('#edit-role');
            var firstName = $.trim($firstNameInput.val());
            var lastName = $.trim($lastNameInput.val());
            var phone = $.trim($phoneInput.val());
            var email = $.trim($emailInput.val());
            var address = $.trim($addressInput.val());
            var role = $.trim($roleInput.val());

            clearEditErrors();

            if (!firstName) {
                $firstNameInput.addClass('is-invalid').focus();
                $('#edit-first-name-error').text('Please enter the first name.');
                return;
            }

            if (!lastName) {
                $lastNameInput.addClass('is-invalid').focus();
                $('#edit-last-name-error').text('Please enter the last name.');
                return;
            }

            if (!phone) {
                $phoneInput.addClass('is-invalid').focus();
                $('#edit-phone-error').text('Please enter the phone number.');
                return;
            }

            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                $emailInput.addClass('is-invalid').focus();
                $('#edit-email-error').text('Please enter a valid email.');
                return;
            }

            if ($roleInput.length && !role) {
                $roleInput.addClass('is-invalid').focus();
                $('#edit-role-error').text('Please select a role.');
                return;
            }

            $submitButton.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route($role . '.update', [$role === 'members' ? 'member' : 'staff' => ':id']) }}".replace(':id', userId),
                type: 'PUT',
                data: $form.serialize(),
                success: function (response) {
                    $('.edit-user-modal').modal('hide');
                    toastr.success(response.message || 'User updated successfully.');
                    if (typeof loadUsersTable === 'function') {
                        loadUsersTable();
                    }
                },
                error: function (xhr) {
                    var message = 'Unable to update user. Please try again.';

                    if (xhr && xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            if (xhr.responseJSON.errors.first_name) {
                                $firstNameInput.addClass('is-invalid');
                                $('#edit-first-name-error').text(xhr.responseJSON.errors.first_name[0]);
                            }
                            if (xhr.responseJSON.errors.last_name) {
                                $lastNameInput.addClass('is-invalid');
                                $('#edit-last-name-error').text(xhr.responseJSON.errors.last_name[0]);
                            }
                            if (xhr.responseJSON.errors.phone) {
                                $phoneInput.addClass('is-invalid');
                                $('#edit-phone-error').text(xhr.responseJSON.errors.phone[0]);
                            }
                            if (xhr.responseJSON.errors.email) {
                                $emailInput.addClass('is-invalid');
                                $('#edit-email-error').text(xhr.responseJSON.errors.email[0]);
                            }
                            if (xhr.responseJSON.errors.address) {
                                $addressInput.addClass('is-invalid');
                                $('#edit-address-error').text(xhr.responseJSON.errors.address[0]);
                            }
                            if (xhr.responseJSON.errors.role) {
                                $roleInput.addClass('is-invalid');
                                $('#edit-role-error').text(xhr.responseJSON.errors.role[0]);
                            }
                        }
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                    }

                    if (!$('#edit-first-name-error').text() && !$('#edit-last-name-error').text() && !$('#edit-phone-error').text() && !$('#edit-email-error').text() && !$('#edit-address-error').text() && !$('#edit-role-error').text()) {
                        toastr.error(message);
                    }
                },
                complete: function () {
                    $submitButton.prop('disabled', false).text('Save');
                }
            });
        });

        $(document).on('click', '.delete-user', function (e) {
            e.preventDefault();

            var userId = $(this).data('id');

            Swal.fire({
                title: 'Delete User',
                text: 'Are you sure you want to delete this user? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route($role . '.destroy', [$role === 'members' ? 'member' : 'staff' => ':id']) }}".replace(':id', userId),
                        type: 'DELETE',
                        success: function (response) {
                            if (typeof loadUsersTable === 'function') {
                                loadUsersTable();
                            }
                            Swal.fire('Deleted!', response.message || 'User deleted successfully.', 'success');
                        },
                        error: function (xhr) {
                            var message = 'Unable to delete user. Please try again.';
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
