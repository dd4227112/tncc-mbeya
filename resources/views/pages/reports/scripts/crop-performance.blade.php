<script>
  var CropPerformanceTable = null;

  function loadCropPerformanceTable(filterData = null) {
    $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-primary';
    CropPerformanceTable = $('#datatable-buttons').DataTable({
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
          data: 'crop_name'
        },
        {
          data: 'total_members'
        },
        {
          data: 'total_weight'
        },
        {
          data: 'average_weight_per_member'
        },
        {
          data: 'total_collection',
          className: 'text-end'
        }
      ],
      order: [
        [0, 'asc']
      ],
      pageLength: 10,
      ajax: {
        url: "{{ route('reports.get-crop_performance-report') }}?" + filterData,
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
    loadCropPerformanceTable(filterData);
  });
  $(function() {
    loadCropPerformanceTable(null);
  });
</script>
