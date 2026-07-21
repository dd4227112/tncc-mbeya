<script>
  var paymentsTable = null;

  function loadPaymentsTableTable(filterData = null) {
    paymentsTable = $('#payments-table').DataTable({
      responsive: true,
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
    fetchInvoiceDetails(invoiceId);
  });

  function fetchInvoiceDetails(invoiceId) {
    $.ajax({
      url: "{{ url('invoices') }}/" + invoiceId + "/details",
      type: 'GET',
      dataType: 'json'
    }).done(function(response) {
      if (response && response.data) {
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
    var modalContent = document.querySelector('.invoice-detail-modal .modal-content');
    if (!modalContent) {
      window.print();
      return;
    }

    // Open a new window and write a minimal document with print-friendly CSS
    var printWindow = window.open('', '_blank', 'width=900,height=1100');

    // Collect existing stylesheet links to preserve visual styling where possible
    var links = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(function(node) {
      return node.outerHTML;
    }).join('\n');

    // Print-specific CSS to force single-page layout (A4) and compact sizing
    var printCss = '\n<style>' +
      '@page { size: A4 portrait; margin: 10mm; }\n' +
      'html, body { width: 210mm; height: 297mm; margin: 0; padding: 0; box-sizing: border-box; }\n' +
      '.invoice-print-wrapper { width: 100%; max-width: 190mm; margin: 0 auto; font-size: 12px; color: #222; }\n' +
      '.invoice-print-wrapper h5, .invoice-print-wrapper h6 { margin: 0 0 6px 0; }\n' +
      '.invoice-print-wrapper .logo-txt { font-size: 16px; }\n' +
      '.invoice-print-wrapper table { width: 100%; border-collapse: collapse; font-size: 12px; }\n' +
      '.invoice-print-wrapper th, .invoice-print-wrapper td { padding: 6px 8px; }\n' +
      '.invoice-print-wrapper .text-end { text-align: right; }\n' +
      '.invoice-print-wrapper .text-center { text-align: center; }\n' +
      '.invoice-print-wrapper .small { font-size: 11px; }\n' +
      '.no-print { display: none !important; }\n' +
      'thead { border-bottom: 1px solid #ddd; }\n' +
      'tfoot { border-top: 1px solid #ddd; }\n' +
      '/* Prevent table rows from breaking across pages */\n' +
      'tr { page-break-inside: avoid; }\n' +
      '</style>\n';

    var html = '<!doctype html><html><head><meta charset="utf-8"><title>Invoice -- TNCC-Mbeya</title>' + links +
      printCss + '</head><body>' +
      '<div class="invoice-print-wrapper">' + modalContent.innerHTML + '</div>' +
      '</body></html>';

    printWindow.document.open();
    printWindow.document.write(html);
    printWindow.document.close();

    // Wait briefly for styles and resources to load, then print
    printWindow.onload = function() {
      try {
        printWindow.focus();
        printWindow.print();
      } catch (e) {
        console.warn('Print failed:', e);
      }
      // close after printing to keep user flow clean
      setTimeout(function() {
        printWindow.close();
      }, 500);
    };
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
