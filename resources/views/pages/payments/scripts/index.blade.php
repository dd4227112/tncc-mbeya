<script>
  var paymentsTable = null;

  function loadPaymentsTableTable(filterData = null) {
    paymentsTable = $('#payments-table').DataTable({
      responsive: false,
      scrollX: true,
      destroy: true,
      processing: true,
      serverSide: false,
      columns: [{
          data: 'id',
          orderable: true,
          searchable: true,
          sortable: false,
        },
        {
          data: 'date'
        },
        {
          data: 'payer'
        },
        {
          data: 'amount',
          className: 'text-end'
        },
        {
          data: 'reference'
        },
        {
          data: 'invoice'
        },
        {
          data: 'location'
        },
        {
          data: 'plate_number'
        },
        {
          data: 'method'
        },
        {
          data: 'status',
          className: 'text-center',
          render: function(data, type, row) {
            return row.status_badge || data;
          }
        },
        {
          data: 'processed'
        },
        {
          data: 'actions',
          orderable: false,
          searchable: false,
          className: 'text-center'
        }
      ],
      order: [
        [0, 'asc']
      ],
      pageLength: 10,
      ajax: {
        url: "{{ route('payments.getpayments') }}?" + filterData,
        type: 'GET',
        dataType: 'json',
        dataSrc: function(json) {
          return (json && json.data) ? json.data : [];
        }
      },
      language: {
        emptyTable: 'No payments available.'
      }
    });
  }

  $('#payments-table').on('click', '.view-payment', function() {
    var paymentId = $(this).data('id');
    fetchpaymentDetails(paymentId);
  });

  $('#payments-table').on('click', '.print-payment', function() {
    var paymentId = $(this).data('id');
    fetchpaymentDetails(paymentId);
  });
  $('#payments-table').on('click', '.push-ussd', function() {
    var invoiceId = $(this).data('id');

    Swal.fire({
      icon: 'warning',
      title: 'Want to send USSD?',
      text: 'This action will request the user to enter PIN to authorize this transaction.',
      showCancelButton: true,
      confirmButtonText: 'Send',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#198754',
      cancelButtonColor: '#6c757d',
      allowOutsideClick: () => !Swal.isLoading(),
      preConfirm: function() {
        Swal.showLoading();

        return $.ajax({
          url: "{{ route('payments.repushPayment', ':id') }}".replace(':id', invoiceId),
          type: 'POST'
        }).catch(function(xhr) {

          var message = 'Unable to send USSD Push. Please try again later.';

          if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          }

          Swal.showValidationMessage(message);
        });
      }
    }).then(function(result) {

      if (result.isConfirmed) {

        Swal.fire({
          icon: 'success',
          title: 'USSD Push Sent',
          text: 'Wait for payment notification.',
          confirmButtonColor: '#198754'
        });

        if (paymentsTable) {
          paymentsTable.ajax.reload(null, false);
        }
      }

    });
  });


  $('#payments-table').on('click', '.delete-payment', function() {
    var paymentId = $(this).data('id');
    Swal.fire({
      icon: 'warning',
      title: 'Delete payment?',
      text: 'This action cannot be undone.',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d'
    }).then(function(result) {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('payments.destroy', ':id') }}".replace(':id', paymentId),
          type: 'DELETE',
        }).done(function() {
          Swal.fire({
            icon: 'success',
            title: 'Deleted',
            text: 'payment deleted successfully.',
            confirmButtonColor: '#5156be'
          });
          if (paymentsTable) {
            paymentsTable.ajax.reload(null, false);
          }
        }).fail(function(xhr) {
          var message = 'Unable to delete payment. Please try again later.';
          if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
          }
          Swal.fire({
            icon: 'error',
            title: 'Delete failed',
            text: message,
            confirmButtonColor: '#5156be'
          });
        });
      }
    });
  });

  $('#payments-table').on('click', '.print-invoice', function() {
    var invoiceId = $(this).data('id');
    window.location.href = "{{ url('invoices') }}/" + invoiceId + "/receipt";
  });

  function printInvoiceDetailModal() {
    var invoiceId = window.__lastInvoiceData && window.__lastInvoiceData.id;
    if (invoiceId) {
      window.location.href = "{{ url('invoices') }}/" + invoiceId + "/receipt";
    }
  }


  $('#filterForm').on('submit', function(e) {
    e.preventDefault();

    var $form = $(this);
    var filterData = $form.serialize();
    loadPaymentsTableTable(filterData);
  });
  $(function() {
    loadPaymentsTableTable(null);
  });
</script>
