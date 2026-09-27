<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invoice {{ $invoice['reference_number'] }} Receipt</title>
<style>
  /* 58mm is the paper width used by handheld POS thermal printers
     (40mm usually refers to the max roll diameter the printer accepts,
     not a fixed print height) — so the page is 58mm wide and grows
     downward with content, like a real receipt roll. */
  @page {
    size: 58mm auto;
    margin: 0;
  }

  * { box-sizing: border-box; }

  html, body {
    margin: 0;
    padding: 0;
    background: #ccc;
  }

  body {
    display: flex;
    justify-content: center;
    font-family: Arial, Helvetica, sans-serif;
  }

  .receipt {
    width: 58mm;
    padding: 3mm 3mm 6mm 3mm;
    background: #fff;
    color: #000;
    opacity: 1;
    font-size: 11px;
    font-weight: 500;
    line-height: 1.4;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .center { text-align: center; }
  .bold   { font-weight: 700; }
  .big    { font-size: 13px; font-weight: 700; letter-spacing: 0.5px; }
  .small  { font-size: 9px; }

  .logo {
    font-size: 17px;
    font-weight: 700;
    letter-spacing: 1px;
  }

  .divider {
    border-top: 1px dashed #000;
    margin: 2mm 0;
  }

  .divider.solid {
    border-top: 1px solid #000;
  }

  .row {
    display: flex;
    justify-content: space-between;
    gap: 4px;
  }

  .items { margin: 1mm 0; }

  .item-name {
    display: flex;
    justify-content: space-between;
  }

  .item-meta {
    display: flex;
    justify-content: space-between;
    color: #000;
    font-size: 9.5px;
    padding-left: 2mm;
  }

  .totals .row { padding: 0.3mm 0; }

  .grand-total {
    font-size: 13px;
    font-weight: 700;
  }

  .barcode {
    margin-top: 3mm;
    text-align: center;
  }

  .barcode svg { width: 100%; height: 34px; }

  .barcode-text {
    letter-spacing: 2px;
    font-size: 10px;
    margin-top: 1mm;
  }

  .qr {
    margin: 2mm auto 0;
    display: block;
  }

  .footer {
    margin-top: 2mm;
    text-align: center;
  }

  /* On-screen preview only: shows the physical edges of the receipt.
     Removed automatically when printing. */
  .preview-frame {
    box-shadow: 0 0 0 1px #999, 0 6px 18px rgba(0,0,0,0.25);
    margin: 16px 0;
  }

  @media print {
    body { background: #fff; }
    .preview-frame { box-shadow: none; margin: 0; }
  }

  .print-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 8px;
    padding: 10px;
    background: #eee;
    border-bottom: 1px solid #ccc;
    z-index: 10;
  }

  .print-btn,
  .back-btn {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
    font-weight: 700;
    padding: 8px 18px;
    border: none;
    border-radius: 6px;
    background: #111;
    color: #fff;
    cursor: pointer;
  }

  .print-btn { background: #111; }
  .back-btn { background: #555; }
  .print-btn:hover { background: #333; }
  .back-btn:hover { background: #444; }

  body { padding-top: 46px; }

  @media print {
    .print-bar { display: none; }
    body { padding-top: 0; }
  }
</style>
</head>
<body>

  <div class="print-bar">
    <button class="back-btn" type="button" onclick="window.history.back()">← Back</button>
    <button class="print-btn" onclick="window.print()">🖨️ Print Receipt</button>
  </div>

  <div class="receipt preview-frame">

    <div class="center logo">TNCC-Kasumulu</div>
    @if (!empty($organizationAddress))
      <div class="center small">{{ $organizationAddress }}</div>
    @endif
    @if (!empty($organizationEmail))
      <div class="center small">Email: {{ $organizationEmail }}</div>
    @endif
    @if (!empty($organizationPhone))
      <div class="center small">Tel: {{ $organizationPhone }}</div>
    @endif
    @if (!empty($invoice['vat']))
      <div class="center small">VAT No: {{ $invoice['vat'] }}</div>
    @endif

    <div class="divider"></div>

    <div class="row"><span>Invoice #</span><span class="bold">{{ $invoice['reference_number'] }}</span></div>
    <div class="row"><span>Date</span><span>{{ $invoice['date'] }}</span></div>
    @if (!empty($invoice['status']))
      <div class="row"><span>Status</span><span>{{ $invoice['status'] }}</span></div>
    @endif
    @if (!empty($invoice['payment_method']))
      <div class="row"><span>Payment</span><span>{{ $invoice['payment_method'] }}</span></div>
    @endif
    @if (!empty($invoice['time']))
      <div class="row"><span>Time</span><span>{{ $invoice['time'] }}</span></div>
    @endif
    @if (!empty($invoice['created_by']))
      <div class="row"><span>Cashier</span><span>{{ $invoice['created_by'] }}</span></div>
    @endif
    @if (!empty($invoice['terminal']))
      <div class="row"><span>Terminal</span><span>{{ $invoice['terminal'] }}</span></div>
    @endif

    <div class="divider"></div>

    <div>
      <div>{{ $invoice['member_name'] }}</div>
      @if (!empty($invoice['member_address']) && $invoice['member_address'] !== '—')
        <div class="small">{{ $invoice['member_address'] }}</div>
      @endif
      @if (!empty($invoice['member_phone']) && $invoice['member_phone'] !== '—')
        <div class="small">Tel: {{ $invoice['member_phone'] }}</div>
      @endif
    </div>

    <div class="items">
      @foreach ($invoice['items'] as $item)
        <div class="item-name"><span>{{ $item['name'] }}</span><span>{{ $item['total_price'] }}</span></div>
        <div class="item-meta"><span>{{ $item['quantity'] }} x {{ $item['unit'] }}</span><span></span></div>
      @endforeach
    </div>

    <div class="divider"></div>

    <div class="totals">
      <div class="row"><span>Subtotal</span><span>{{ $invoice['sub_total'] }}</span></div>
      @if (!empty($invoice['discount']))
        <div class="row"><span>Discount</span><span>{{ $invoice['discount'] }}</span></div>
      @endif
      @if (!empty($invoice['vat']))
        <div class="row"><span>VAT</span><span>{{ $invoice['vat'] }}</span></div>
      @endif
      <div class="divider"></div>
      <div class="row grand-total"><span>TOTAL</span><span>TZS {{ $invoice['total_amount'] }}</span></div>
    </div>

    @if (!empty($invoice['paid_amount']) || !empty($invoice['change']))
      <div class="divider"></div>
      <div class="totals">
        @if (!empty($invoice['paid_amount']))
          <div class="row"><span>Paid</span><span>{{ $invoice['paid_amount'] }}</span></div>
        @endif
        @if (!empty($invoice['change']))
          <div class="row"><span>Change</span><span>{{ $invoice['change'] }}</span></div>
        @endif
      </div>
    @endif

    <div class="divider solid"></div>

    @if (!empty($invoice['barcode']))
      <div class="barcode">
        <div class="barcode-text">{{ $invoice['barcode'] }}</div>
      </div>
    @endif

    <div class="center">------- END OF VALID RECEIPT -------</div>

  </div>

  <script>
    window.addEventListener('load', function () {
      setTimeout(function () {
        window.print();
      }, 500);
    });
  </script>
</body>
</html>