<script>
  var SEARCH_ROUTES = {
    crop: '{{ route('crops.searchCrop', ['term' => 'SEARCH_TERM']) }}',
    member: '{{ route('members.searchMember', ['query' => 'SEARCH_QUERY']) }}'
  };

  var SAVE_ROUTE = '{{ route('invoices.store') }}';
  var selectedMember = null;
  var cropRows = [];

  function formatAmount(value) {
    var amount = parseFloat(value);
    if (isNaN(amount)) {
      amount = 0;
    }
    return amount.toLocaleString('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  function formatInitials(name) {
    var parts = name.split(' ');
    var initials = '';

    for (var i = 0; i < parts.length && i < 2; i++) {
      if (parts[i].length > 0) {
        initials += parts[i].charAt(0).toUpperCase();
      }
    }

    return initials;
  }

  function buildMemberSearchUrl(query) {
    return SEARCH_ROUTES.member.replace('SEARCH_QUERY', encodeURIComponent(query));
  }

  function buildCropSearchUrl(query) {
    return SEARCH_ROUTES.crop.replace('SEARCH_TERM', encodeURIComponent(query));
  }

  function renderMemberResults(items) {
    var $results = $('#memberResults');
    $results.empty();

    if (!items || items.length === 0) {
      $results.append(
        '<div class="search-result-empty">No results found.<br/><a href="#"data-bs-toggle="modal" data-bs-target=".add-user-modal" class="btn btn-primary btn-sm">Add New</a></div>');
      $results.addClass('show');
      return;
    }

    for (var i = 0; i < items.length && i < 8; i++) {
      var member = items[i];
      var $item = $('<div class="search-result-item"></div>');
      var itemHtml = '<div class="item-name">' + member.name + '</div>' +
        '<div class="item-sub">' + (member.email || member.phone || '') + '</div>';

      $item.html(itemHtml);
      (function(currentMember) {
        $item.on('click', function() {
          selectMember({
            id: currentMember.id,
            name: currentMember.name,
            farm: currentMember.address || '—',
            location: currentMember.address || '—',
            phone: currentMember.phone || '—',
            status: currentMember.roles || 'Member'
          });
          $results.removeClass('show');
        });
      })(member);

      $results.append($item);
    }

    $results.addClass('show');
  }

  function renderCropResults(items) {
    var $results = $('#cropResults');
    $results.empty();

    if (!items || items.length === 0) {
      $results.append('<div class="search-result-empty">No results found. <br/><a href="#"data-bs-toggle="modal" data-bs-target=".add-crop-modal" class="btn btn-primary btn-sm">Add New</a></div>');
      $results.addClass('show');
      return;
    }

    for (var i = 0; i < items.length && i < 8; i++) {
      var crop = items[i];
      var $item = $('<div class="search-result-item"></div>');
      var itemHtml = '<div class="item-name">' + crop.name + ' (' + crop.description + ')</div>' +
        '<div class="item-sub">' + formatAmount(crop.price) + ' / ' + crop.unit + '</div>';

      $item.html(itemHtml);
      (function(currentCrop) {
        $item.on('click', function() {
          addCrop({
            id: currentCrop.id,
            name: currentCrop.name+' ('+currentCrop.description+')',
            price: currentCrop.price,
            unit: currentCrop.unit
          });
          $results.removeClass('show');
        });
      })(crop);

      $results.append($item);
    }

    $results.addClass('show');
  }

  function selectMember(member) {
    selectedMember = member;
    $('#memberPlaceholder').addClass('d-none');
    $('#memberDetails').removeClass('d-none');
    $('#memberCard').addClass('has-member');

    $('#memberAvatar').text(formatInitials(member.name));
    $('#memberName').text(member.name);
    $('#memberFarm').text(member.farm);
    $('#memberLocation').text(member.location);
    $('#memberPhone').text(member.phone);
    $('#memberId').text(member.id);
  }

  function clearMember() {
    selectedMember = null;
    $('#memberDetails').addClass('d-none');
    $('#memberPlaceholder').removeClass('d-none');
    $('#memberCard').removeClass('has-member');
  }

  function resetInvoiceForm() {
    clearMember();
    cropRows = [];
    renderCropTable();
    $('#memberSearchInput').val('');
    $('#memberResults').empty().removeClass('show');
    $('#cropSearchInput').val('');
    $('#cropResults').empty().removeClass('show');
  }

  function searchMembers(query) {
    $.ajax({
      url: buildMemberSearchUrl(query),
      type: 'GET',
      dataType: 'json'
    }).done(function(response) {
      var members = [];
      if (response && $.isArray(response.data)) {
        members = response.data;
      }
      renderMemberResults(members);
    }).fail(function() {
      $('#memberResults').empty().append('<div class="search-result-empty">Unable to load members.</div>').addClass(
        'show');
    });
  }

  function searchCrops(query) {
    $.ajax({
      url: buildCropSearchUrl(query),
      type: 'GET',
      dataType: 'json'
    }).done(function(response) {
      var crops = [];
      if (response && $.isArray(response.data)) {
        crops = response.data;
      }
      renderCropResults(crops);
    }).fail(function() {
      $('#cropResults').empty().append('<div class="search-result-empty">Unable to load crops.</div>').addClass(
        'show');
    });
  }

  function addCrop(crop) {
    var existing = null;
    for (var i = 0; i < cropRows.length; i++) {
      if (cropRows[i].id == crop.id) {
        existing = cropRows[i];
        break;
      }
    }

    if (existing) {
      existing.quantity = existing.quantity + 1;
    } else {
      cropRows.push({
        id: crop.id,
        name: crop.name,
        price: parseFloat(crop.price),
        unit: crop.unit,
        quantity: 1
      });
    }

    renderCropTable();
  }

  function initMemberSearch() {
    var $input = $('#memberSearchInput');
    var $results = $('#memberResults');
    var searchTimer;

    $input.on('input focus', function() {
      clearTimeout(searchTimer);
      var query = $.trim($input.val());
      if (query.length < 3) {
        $results.empty().removeClass('show');
        return;
      }
      searchTimer = setTimeout(function() {
        searchMembers(query);
      }, 300);
    });

    $(document).on('click', function(event) {
      if (!$(event.target).closest('#memberResults, #memberSearchInput').length) {
        $results.removeClass('show');
      }
    });
  }

  function initCropSearch() {
    var $input = $('#cropSearchInput');
    var $results = $('#cropResults');
    var searchTimer;

    $input.on('input focus', function() {
      clearTimeout(searchTimer);
      var query = $.trim($input.val());
      if (query.length < 3) {
        $results.empty().removeClass('show');
        return;
      }
      searchTimer = setTimeout(function() {
        searchCrops(query);
      }, 300);
    });

    $(document).on('click', function(event) {
      if (!$(event.target).closest('#cropResults, #cropSearchInput').length) {
        $results.removeClass('show');
      }
    });
  }

  function removeCrop(id) {
    var newRows = [];
    for (var i = 0; i < cropRows.length; i++) {
      if (cropRows[i].id != id) {
        newRows.push(cropRows[i]);
      }
    }
    cropRows = newRows;
    renderCropTable();
  }

  function updateQuantity(id, value) {
    var qty = parseFloat(value);
    if (isNaN(qty) || qty < 1) {
      qty = 1;
    }

    for (var i = 0; i < cropRows.length; i++) {
      if (cropRows[i].id == id) {
        cropRows[i].quantity = qty;
        break;
      }
    }

    renderCropTable(false);
  }

  function renderCropTable(rebuild) {
    if (typeof rebuild === 'undefined') {
      rebuild = true;
    }

    var $tbody = $('#cropTableBody');
    var $emptyRow = $('#emptyRow');

    if (cropRows.length === 0) {
      $tbody.empty().append($emptyRow);
      updateSummary();
      return;
    }

    if (rebuild) {
      $tbody.empty();
      for (var i = 0; i < cropRows.length; i++) {
        var row = cropRows[i];
        var rowHtml =
          '<tr data-id="' + row.id + '">' +
          '<th scope="row">' + ('0' + (i + 1)).slice(-2) + '</th>' +
          '<td><h5 class="font-size-14 mb-0">' + row.name + '</h5></td>' +
          '<td class="text-end">' + formatAmount(row.price) + '</td>' +
          '<td>' + row.unit + '</td>' +
          '<td>' +
          '<input type="number" min="1" step="any" class="form-control form-control-sm qty-input" ' +
          'value="' + row.quantity + '" data-id="' + row.id + '">' +
          '</td>' +
          '<td class="text-end row-amount">' + formatAmount(row.price * row.quantity) + '</td>' +
          '<td class="text-center">' +
          '<button type="button" class="btn-remove-row" data-id="' + row.id + '" title="Remove crop">' +
          '<i class="mdi mdi-trash-can-outline"></i>' +
          '</button>' +
          '</td>' +
          '</tr>';

        $tbody.append(rowHtml);
      }

      $tbody.find('.qty-input').on('input', function() {
        updateQuantity($(this).data('id'), $(this).val());
      });

      $tbody.find('.btn-remove-row').on('click', function() {
        removeCrop($(this).data('id'));
      });
    } else {
      for (var i = 0; i < cropRows.length; i++) {
        var row = cropRows[i];
        var $tr = $tbody.find('tr[data-id="' + row.id + '"]');
        if ($tr.length) {
          $tr.find('.row-amount').text(formatAmount(row.price * row.quantity));
        }
      }
    }

    updateSummary();
  }

  function updateSummary() {
    var subTotal = 0;
    for (var i = 0; i < cropRows.length; i++) {
      subTotal += cropRows[i].price * cropRows[i].quantity;
    }

    $('#summaryItemCount').text(cropRows.length);
    $('#summarySubTotal').text(formatAmount(subTotal));
    $('#summaryTotal').text(formatAmount(subTotal));
  }

  function saveInvoice() {
    if (!selectedMember || !selectedMember.id) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing member',
        text: 'Please select a member before saving the invoice.',
        confirmButtonColor: '#5156be'
      });
      return;
    }

    if (cropRows.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No crops added',
        text: 'Please add at least one crop before saving the invoice.',
        confirmButtonColor: '#5156be'
      });
      return;
    }

    for (var i = 0; i < cropRows.length; i++) {
      if (cropRows[i].quantity < 1) {
        Swal.fire({
          icon: 'warning',
          title: 'Invalid quantity',
          text: 'Each crop quantity must be at least 1.',
          confirmButtonColor: '#5156be'
        });
        return;
      }
    }

    var subTotal = 0;
    for (var i = 0; i < cropRows.length; i++) {
      subTotal += cropRows[i].price * cropRows[i].quantity;
    }

    var payload = {
      member_id: selectedMember.id,
      items: cropRows.map(function(row) {
        return {
          crop_id: row.id,
          quantity: row.quantity,
          unit_price: row.price,
          total_price: row.price * row.quantity
        };
      }),
      total_amount: subTotal
    };

    var selectedMemberName = selectedMember.name;
    var formattedTotal = formatAmount(subTotal);

    $.ajax({
      url: SAVE_ROUTE,
      type: 'POST',
      dataType: 'json',
      contentType: 'application/json',
      data: JSON.stringify(payload)
    }).done(function(response) {
      resetInvoiceForm();
      $('.add-invoice-modal').modal('hide');
      if (typeof invoicesTable !== 'undefined' && invoicesTable) {
        invoicesTable.ajax.reload(null, false);
      }
      Swal.fire({
        icon: 'success',
        title: 'Invoice saved',
        html: 'Invoice saved for <strong>' + selectedMemberName + '</strong><br>Total: <strong>' +
          formattedTotal + '</strong>',
        confirmButtonColor: '#5156be'
      }).then((result) => {
        if (result.isConfirmed && response?.data?.id) {
          getInvoiceDetails(response.data.id);
        }
      });
    }).fail(function(xhr) {
      var message = 'Unable to save invoice. Please try again.';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
      }
      Swal.fire({
        icon: 'error',
        title: 'Save failed',
        text: message,
        confirmButtonColor: '#5156be'
      });
    });
  }

  $(function() {
    initMemberSearch();
    initCropSearch();
    $('#clearMemberBtn').on('click', clearMember);
    $('#saveInvoiceBtn').on('click', saveInvoice);
    renderCropTable();
  });
</script>
