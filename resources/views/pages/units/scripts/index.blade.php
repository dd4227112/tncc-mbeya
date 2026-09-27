<script>
  function loadUnitsTable() {
    var table = $('#units-table').DataTable({
      responsive: false,
      scrollX: true,
      destroy: true,
      processing: true,
      columns: [{
          data: 'id',
          orderable: false,
          searchable: true,
          sortable: false,
        },
        {
          data: 'name'
        },
        {
          data: 'abbreviation'
        },
        {
          data: 'actions',
          orderable: false,
          searchable: false
        }
      ],
      order: [
        [0, 'asc']
      ],
      pageLength: 10
    });

    $.ajax({
      url: '{{ route('units.getUnits') }}',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        var rows = response && response.data ? response.data : [];
        table.clear().rows.add(rows).draw();
      },
      error: function(xhr) {
        var message = 'Unable to load units at the moment. Please try again later.';

        if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        }

        table.clear().draw();
        toastr.error(message);
      }
    });
  }

  $(function() {
    loadUnitsTable();
  });
</script>
