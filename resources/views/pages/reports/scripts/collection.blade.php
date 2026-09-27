<script>
  var collectionTable = null;

  function loadCollectionReportTable(filterData = null) {
    $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-primary';
    collectionTable = $('#datatable-buttons').DataTable({
      responsive: false,
      scrollX: true,
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
        emptyTable: 'No report data available.'
      }
    });
  }

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
