<script>
  function resetAddPaymentForm() {
    $('#addPaymentForm')[0].reset();
    $('#addPaymentForm').find('.is-invalid').removeClass('is-invalid');
    $('#addPaymentForm').find('.invalid-feedback').remove();
  }
  //    function showAddPaymentModal(invoiceId) {
  //     resetAddPaymentForm();
  //     getInvoiceDetails(invoiceId);
  //     $('.add-payment-modal').modal('show');
  //   }
  $('#invoices-table').on('click', '.add-payment', function() {
    var invoiceId = $(this).data('id');
    getInvoiceDetails(invoiceId);
  });

  function getInvoiceDetails(invoiceId) {
    $.ajax({
      url: "{{ url('invoices') }}/" + invoiceId + "/details",
      type: 'GET',
      dataType: 'json'
    }).done(function(response) {
      if (response && response.data) {
        renderPaymentDetailModal(response.data);
        $('.add-payment-modal').modal('show');
      }
    }).fail(function(xhr) {
      var message = 'Unable to load invoice details. Please try again.';
      if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
      }
      Swal.fire({
        icon: 'error',
        title: 'Load failed',
        text: message,
        confirmButtonColor: '#5156be'
      });
    });
  }

  function renderPaymentDetailModal(invoice) {
    $('#invoiceDetail').text(invoice.reference_number + ' | Date: ' + invoice.date);
    $('#memberDetail').text(invoice.member_name + ' | Phone: ' + invoice.member_phone);
    $('#add-amount').val(invoice.total_amount);
    $('#add-phone').val(invoice.member_phone);
    $('#add-invoice-id').val(invoice.id);
    // $('#invoiceDetailBilledName').text(invoice.member_name);
    // $('#invoiceDetailBilledAddress').text(invoice.member_address);
    // $('#invoiceDetailBilledEmail').text(invoice.member_email);
    // $('#invoiceDetailBilledPhone').text(invoice.member_phone);
    // $('#invoiceDetailOrderDate').text(invoice.date);
    // $('#invoiceDetailPaymentMethod').text('Not specified');
    // $('#invoiceDetailPaymentMethodNote').text('');
  }

  $(function() {
    $('#addPaymentForm').on('submit', function(event) {
      event.preventDefault();
      var $form = $(this);
      var formData = $form.serialize();
      var $submitButton = $form.find('button[type="submit"]');
      $form.find('.is-invalid').removeClass('is-invalid');
      $form.find('.invalid-feedback').text('');
      var phone = $.trim($('#add-phone').val());
      var network = $.trim($('#add-network').val());
      var method = $.trim($('#add-method').val());
      var invoiceId = $.trim($('#add-invoice-id').val());

      if (!phone) {
        $('#add-phone').addClass('is-invalid').focus();
        $('#add-phone-error').text('Please enter the phone number.');
        return;
      }
      if (!network) {
        $('#add-network').addClass('is-invalid').focus();
        $('#add-network-error').text('Please select a network.');
        return;
      }
      if (!method) {
        $('#add-method').addClass('is-invalid').focus();
        $('#add-method-error').text('Please select a payment method.');
        return;
      }
      if (!invoiceId) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Invoice ID is missing. Please try again.',
          confirmButtonColor: '#5156be'
        });
        return;
      }
      $submitButton.prop('disabled', true).text('Creating Payment...');
      $.ajax({
        url: "{{ route('payments.store') }}",
        type: 'POST',
        data: formData,
        dataType: 'json',
        // contentType: 'application/json',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
      }).done(function(response) {
        $submitButton.prop('disabled', false).text('Save');
        $('.add-payment-modal').modal('hide');
        resetAddPaymentForm();
        Swal.fire({
          icon: 'success',
          title: 'Payment Received',
          text: 'Payment has been successfully received.',
          confirmButtonColor: '#5156be'
        }).then((result) => {
          if (result.isConfirmed && response?.data?.id) {
            fetchInvoiceDetails(response.data.id);
          }
        });

      }).fail(function(xhr) {
        $submitButton.prop('disabled', false).text('Save');
        var message = 'Unable to save payment. Please try again.';
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        }
        Swal.fire({
          icon: 'error',
          title: 'Save failed',
          text: message,
          confirmButtonColor: '#5156be'
        })
      });
    });
  });
</script>
