<script>
  var invoicesTable = null;

  function loadInvoicesTable(filterData = null) {
    invoicesTable = $('#invoices-table').DataTable({
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
          data: 'reference_number'
        },
        {
          data: 'date'
        },
        {
          data: 'member'
        },
        {
          data: 'total_amount',
          className: 'text-end'
        },
        {
          data: 'status',
          className: 'text-center',
          render: function(data, type, row) {
            return row.status_badge || data;
          }
        },
        {
          data: 'created_by'
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
        url: "{{ route('invoices.getInvoices') }}" + (filterData ? '?' + filterData : ''),
        type: 'GET',
        dataType: 'json',
        dataSrc: function(json) {
          return (json && json.data) ? json.data : [];
        }
      },
      language: {
        emptyTable: 'No invoices available yet.'
      }
    });
  }

  $('#invoices-table').on('click', '.view-invoice', function() {
    var invoiceId = $(this).data('id');
    fetchInvoiceDetails(invoiceId);
  });

  $('#invoices-table').on('click', '.print-invoice', function() {
    var invoiceId = $(this).data('id');
    window.location.href = "{{ url('invoices') }}/" + invoiceId + "/receipt";
  });

  $('#invoices-table').on('click', '.delete-invoice', function() {
    var invoiceId = $(this).data('id');
    Swal.fire({
      icon: 'warning',
      title: 'Delete invoice?',
      text: 'This action cannot be undone.',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d'
    }).then(function(result) {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('invoices.destroy', ':id') }}".replace(':id', invoiceId),
          type: 'DELETE'
        }).done(function() {
          Swal.fire({
            icon: 'success',
            title: 'Deleted',
            text: 'Invoice deleted successfully.',
            confirmButtonColor: '#5156be'
          });
          if (invoicesTable) {
            invoicesTable.ajax.reload(null, false);
          }
        }).fail(function(xhr) {
          var message = 'Unable to delete invoice. Please try again later.';
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

  function fetchInvoiceDetails(invoiceId) {
    $.ajax({
      url: "{{ url('invoices') }}/" + invoiceId + "/details",
      type: 'GET',
      dataType: 'json'
    }).done(function(response) {
      if (response && response.data) {
        window.__lastInvoiceData = response.data;
        renderInvoiceDetailModal(response.data);
        $('.invoice-detail-modal').modal('show');
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

  function renderInvoiceDetailModal(invoice) {
    window.__lastInvoiceData = invoice; // cache for receipt navigation from the detail modal

    $('#invoiceDetailNumber').text('Invoice # ' + invoice.reference_number);
    $('#invoiceDetailAddress').text(invoice.member_address);
    $('#invoiceDetailEmail').html('<i class="mdi mdi-email align-middle me-1"></i> ' + invoice.member_email);
    $('#invoiceDetailPhone').html('<i class="mdi mdi-phone align-middle me-1"></i> ' + invoice.member_phone);
    $('#invoiceDetailBilledName').text(invoice.member_name);
    $('#invoiceDetailBilledAddress').text(invoice.member_address);
    $('#invoiceDetailBilledEmail').text(invoice.member_email);
    $('#invoiceDetailBilledPhone').text(invoice.member_phone);
    $('#invoiceDetailOrderDate').text(invoice.date);
    $('#invoiceDetailStatus').text(invoice.status);
    $('#invoiceDetailPaymentReference').text(invoice.transaction_reference);
    $('#invoiceDetailPaymentStatus').text(invoice.payment_status);
    $('#invoiceDetailPaymentMethod').text(invoice.payment_method);
    $('#invoiceDetailLocation').text(invoice.location || '—');
    $('#invoiceDetailPlateNumber').text(invoice.plate_number || '—');

    var $body = $('#invoiceDetailItemsBody');
    $body.empty();
    invoice.items.forEach(function(item) {
      $body.append(
        '<tr>' +
        '<th scope="row">' + item.index + '</th>' +
        '<td>' + item.name + '<p class="font-size-13 text-muted mb-0">' + item.quantity + ' x ' + item.unit +
        '</p></td>' +
        '<td class="text-end">' + item.total_price + '</td>' +
        '</tr>'
      );
    });

    $('#invoiceDetailSubTotal').text(invoice.sub_total);
    $('#invoiceDetailTotal').text(invoice.total_amount);
  }

  function printInvoiceDetailModal() {
    var invoiceId = window.__lastInvoiceData && window.__lastInvoiceData.id;
    if (!invoiceId) {
      Swal.fire({
        icon: 'error',
        title: 'Invoice unavailable',
        text: 'Load the invoice details before printing.',
        confirmButtonColor: '#5156be'
      });
      return;
    }
    window.location.href = "{{ url('invoices') }}/" + invoiceId + "/receipt";
  }

  $('#filterForm').on('submit', function(e) {
    e.preventDefault();

    var $form = $(this);
    var filterData = $form.serialize();
    loadInvoicesTable(filterData);
  });

  $(function() {
    $('.invoice-detail-modal').on('hidden.bs.modal', function() {
      if (invoicesTable) {
        invoicesTable.ajax.reload(null, false);
      }
    });
    loadInvoicesTable(null);
  });
</script>