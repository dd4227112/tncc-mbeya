
<script>
  // ---------------------------------------------------------------
  // Mock data — replace with real API calls when wiring this up.
  // ---------------------------------------------------------------
  const MEMBERS = [{
      id: "MB-1001",
      name: "Richard Saul",
      farm: "Sunrise Family Farm",
      location: "Lafayette, LA",
      phone: "337-256-9134",
      status: "Active"
    },
    {
      id: "MB-1002",
      name: "Grace Mushi",
      farm: "Green Valley Cooperative",
      location: "Mbeya, Tanzania",
      phone: "0754-112-334",
      status: "Active"
    },
    {
      id: "MB-1003",
      name: "James Lemire",
      farm: "Lemire Estates",
      location: "Baton Rouge, LA",
      phone: "225-771-4420",
      status: "Active"
    },
    {
      id: "MB-1004",
      name: "Amina Kombo",
      farm: "Kombo Agro Ventures",
      location: "Morogoro, Tanzania",
      phone: "0713-556-902",
      status: "Inactive"
    },
    {
      id: "MB-1005",
      name: "Peter Mwakalinga",
      farm: "Highland Growers",
      location: "Iringa, Tanzania",
      phone: "0788-221-045",
      status: "Active"
    },
    {
      id: "MB-1006",
      name: "Linda Foster",
      farm: "Foster Family Farm",
      location: "Opelousas, LA",
      phone: "337-902-8871",
      status: "Active"
    },
  ];

  const CROPS = [{
      id: "CR-01",
      name: "Maize",
      price: 0.42,
      unit: "kg"
    },
    {
      id: "CR-02",
      name: "Rice (Paddy)",
      price: 0.55,
      unit: "kg"
    },
    {
      id: "CR-03",
      name: "Coffee (Arabica)",
      price: 4.20,
      unit: "kg"
    },
    {
      id: "CR-04",
      name: "Cashew Nuts",
      price: 1.85,
      unit: "kg"
    },
    {
      id: "CR-05",
      name: "Sesame Seeds",
      price: 1.10,
      unit: "kg"
    },
    {
      id: "CR-06",
      name: "Sunflower Seeds",
      price: 0.65,
      unit: "kg"
    },
    {
      id: "CR-07",
      name: "Cotton (Lint)",
      price: 1.95,
      unit: "kg"
    },
    {
      id: "CR-08",
      name: "Soybeans",
      price: 0.78,
      unit: "kg"
    },
    {
      id: "CR-09",
      name: "Cassava",
      price: 0.28,
      unit: "kg"
    },
    {
      id: "CR-10",
      name: "Groundnuts",
      price: 1.05,
      unit: "kg"
    },
  ];

  const TAX_RATE = 0.05;

  let selectedMember = null;
  let cropRows = []; // { id, name, price, unit, quantity }

  const fmt = (n) => "$" + n.toFixed(2);

  // ---------------------------------------------------------------
  // Generic search-dropdown behaviour
  // ---------------------------------------------------------------
  function setupSearch({
    inputEl,
    resultsEl,
    dataset,
    renderItem,
    matches,
    onSelect
  }) {
    function render(query) {
      const q = query.trim().toLowerCase();
      const results = q === "" ? [] : dataset.filter((item) => matches(item, q)).slice(0, 8);

      resultsEl.innerHTML = "";
      if (q === "") {
        resultsEl.classList.remove("show");
        return;
      }
      if (results.length === 0) {
        const div = document.createElement("div");
        div.className = "search-result-empty";
        div.textContent = "No results found.";
        resultsEl.appendChild(div);
        resultsEl.classList.add("show");
        return;
      }
      results.forEach((item) => {
        const div = document.createElement("div");
        div.className = "search-result-item";
        div.innerHTML = renderItem(item);
        div.addEventListener("click", () => {
          onSelect(item);
          resultsEl.classList.remove("show");
          inputEl.value = "";
        });
        resultsEl.appendChild(div);
      });
      resultsEl.classList.add("show");
    }

    inputEl.addEventListener("input", () => render(inputEl.value));
    inputEl.addEventListener("focus", () => render(inputEl.value));
    document.addEventListener("click", (e) => {
      if (!resultsEl.contains(e.target) && e.target !== inputEl) {
        resultsEl.classList.remove("show");
      }
    });
  }

  // ---------------------------------------------------------------
  // Member search + details
  // ---------------------------------------------------------------
  function initials(name) {
    return name.split(" ").map((p) => p[0]).slice(0, 2).join("").toUpperCase();
  }

  function selectMember(member) {
    selectedMember = member;
    document.getElementById("memberPlaceholder").classList.add("d-none");
    const details = document.getElementById("memberDetails");
    details.classList.remove("d-none");
    document.getElementById("memberCard").classList.add("has-member");

    document.getElementById("memberAvatar").textContent = initials(member.name);
    document.getElementById("memberName").textContent = member.name;
    document.getElementById("memberFarm").textContent = member.farm;
    document.getElementById("memberLocation").textContent = member.location;
    document.getElementById("memberPhone").textContent = member.phone;
    document.getElementById("memberId").textContent = member.id;

    const statusEl = document.getElementById("memberStatus");
    statusEl.textContent = member.status;
    statusEl.className = "badge " + (member.status === "Active" ? "badge-soft-success" : "bg-secondary");
  }

  document.getElementById("clearMemberBtn").addEventListener("click", () => {
    selectedMember = null;
    document.getElementById("memberDetails").classList.add("d-none");
    document.getElementById("memberPlaceholder").classList.remove("d-none");
    document.getElementById("memberCard").classList.remove("has-member");
  });

  setupSearch({
    inputEl: document.getElementById("memberSearchInput"),
    resultsEl: document.getElementById("memberResults"),
    dataset: MEMBERS,
    matches: (m, q) => m.name.toLowerCase().includes(q) || m.phone.includes(q) || m.farm.toLowerCase().includes(q),
    renderItem: (m) => `
                <div class="item-name">${m.name}</div>
                <div class="item-sub">${m.farm} · ${m.phone}</div>
            `,
    onSelect: selectMember,
  });

  // ---------------------------------------------------------------
  // Crop search + table
  // ---------------------------------------------------------------
  function addCrop(crop) {
    const existing = cropRows.find((r) => r.id === crop.id);
    if (existing) {
      existing.quantity += 1;
    } else {
      cropRows.push({
        id: crop.id,
        name: crop.name,
        price: crop.price,
        unit: crop.unit,
        quantity: 1
      });
    }
    renderCropTable();
  }

  function removeCrop(id) {
    cropRows = cropRows.filter((r) => r.id !== id);
    renderCropTable();
  }

  function updateQuantity(id, value) {
    const row = cropRows.find((r) => r.id === id);
    if (!row) return;
    let qty = parseFloat(value);
    if (isNaN(qty) || qty < 0) qty = 0;
    row.quantity = qty;
    renderCropTable(false); // don't rebuild inputs, just update totals, to keep focus
  }

  function renderCropTable(rebuild = true) {
    const tbody = document.getElementById("cropTableBody");
    const emptyRow = document.getElementById("emptyRow");

    if (cropRows.length === 0) {
      tbody.innerHTML = "";
      tbody.appendChild(emptyRow);
      updateSummary();
      return;
    }

    if (rebuild) {
      tbody.innerHTML = "";
      cropRows.forEach((row, idx) => {
        const tr = document.createElement("tr");
        tr.dataset.id = row.id;
        tr.innerHTML = `
                        <th scope="row">${String(idx + 1).padStart(2, "0")}</th>
                        <td>
                            <h5 class="font-size-14 mb-0">${row.name}</h5>
                        </td>
                        <td class="text-end">${fmt(row.price)}</td>
                        <td>${row.unit}</td>
                        <td>
                            <input type="number" min="0" step="any" class="form-control form-control-sm qty-input"
                                   value="${row.quantity}" data-id="${row.id}">
                        </td>
                        <td class="text-end row-amount">${fmt(row.price * row.quantity)}</td>
                        <td class="text-center">
                            <button type="button" class="btn-remove-row" data-id="${row.id}" title="Remove crop">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </td>
                    `;
        tbody.appendChild(tr);
      });

      tbody.querySelectorAll(".qty-input").forEach((input) => {
        input.addEventListener("input", (e) => {
          updateQuantity(e.target.dataset.id, e.target.value);
        });
      });
      tbody.querySelectorAll(".btn-remove-row").forEach((btn) => {
        btn.addEventListener("click", (e) => {
          removeCrop(e.currentTarget.dataset.id);
        });
      });
    } else {
      // update amounts in place without losing input focus
      cropRows.forEach((row) => {
        const tr = tbody.querySelector(`tr[data-id="${row.id}"]`);
        if (tr) {
          tr.querySelector(".row-amount").textContent = fmt(row.price * row.quantity);
        }
      });
    }

    updateSummary();
  }

  function updateSummary() {
    const subTotal = cropRows.reduce((sum, r) => sum + r.price * r.quantity, 0);
    const tax = subTotal * TAX_RATE;
    const total = subTotal + tax;

    document.getElementById("summaryItemCount").textContent = cropRows.length;
    document.getElementById("summarySubTotal").textContent = fmt(subTotal);
    document.getElementById("summaryTax").textContent = fmt(tax);
    document.getElementById("summaryTotal").textContent = fmt(total);
  }

  setupSearch({
    inputEl: document.getElementById("cropSearchInput"),
    resultsEl: document.getElementById("cropResults"),
    dataset: CROPS,
    matches: (c, q) => c.name.toLowerCase().includes(q),
    renderItem: (c) => `
                <div class="item-name">${c.name}</div>
                <div class="item-sub">${fmt(c.price)} / ${c.unit}</div>
            `,
    onSelect: addCrop,
  });

  // ---------------------------------------------------------------
  // Save
  // ---------------------------------------------------------------
  document.getElementById("saveInvoiceBtn").addEventListener("click", () => {
    if (!selectedMember) {
      alert("Please select a member before saving the invoice.");
      return;
    }
    if (cropRows.length === 0) {
      alert("Please add at least one crop before saving the invoice.");
      return;
    }
    const subTotal = cropRows.reduce((sum, r) => sum + r.price * r.quantity, 0);
    const tax = subTotal * TAX_RATE;
    const total = subTotal + tax;

    console.log("Invoice payload:", {
      member: selectedMember,
      items: cropRows,
      subTotal,
      tax,
      total,
    });
    alert(`Invoice saved for ${selectedMember.name}\nTotal: ${fmt(total)}`);
  });

  // initial render
  renderCropTable();
</script>
