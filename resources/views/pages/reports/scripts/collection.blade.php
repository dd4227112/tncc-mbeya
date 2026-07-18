<script>
  var paymentsTable = null;

  function loadCollectionReportTable(filterData = null) {
    $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-primary';
    paymentsTable = $('#datatable-buttons').DataTable({
      responsive: true,
      destroy: true,
      processing: true,
      serverSide: false,
      dom: 'Bfrtip',
      buttons: [{
          extend: 'copy',
          className: 'btn btn-primary'
        },
        {
          extend: 'excel',
          className: 'btn btn-primary'
        },
        {
          extend: 'pdf',
          className: 'btn btn-primary'
        },
        {
          extend: 'colvis',
          className: 'btn btn-primary'
        }
      ],
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
          data: 'reference'
        },
        {
          data: 'member'
        },
        {
          data: 'crop'
        },
        {
          data: 'quantity',
          className: 'text-end'
        },
        {
          data: 'rate',
          className: 'text-end'
        },
        {
          data: 'amount',
          className: 'text-end'
        },
        {
          data: 'user'
        }
      ],
      order: [
        [0, 'asc']
      ],
      pageLength: 10,
      ajax: {
        url: "{{ route('reports.getCollection') }}?" + filterData,
        type: 'GET',
        dataType: 'json',
        dataSrc: function(json) {
            $('#reportDate').text(json.title);
          return (json && json.data) ? json.data : [];
        }
      },
      language: {
        emptyTable: 'No payments available.'
      }
    });

    $('#datatable-buttons').on('click', '.view-payment', function() {
      var paymentId = $(this).data('id');
      fetchpaymentDetails(paymentId);
    });

    $('#datatable-buttons').on('click', '.print-payment', function() {
      var paymentId = $(this).data('id');
      fetchpaymentDetails(paymentId);
    });
  }
  $('#collection-report-table').on('click', '.push-ussd', function() {
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
          type: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
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
  $('#filterForm').on('submit', function(e) {
    e.preventDefault();

    var $form = $(this);
    var filterData = $form.serialize();
    loadCollectionReportTable(filterData);
  });
  $(function() {
    loadCollectionReportTable(null);
  });
</script>
