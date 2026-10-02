<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Verification - {{ $invoice->reference_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 32px 16px;
            background: #f3f5f8;
            color: #172033;
            font-family: Arial, Helvetica, sans-serif;
        }
        .card {
            max-width: 520px;
            margin: 0 auto;
            padding: 32px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 12px 36px rgba(24, 39, 75, .12);
        }
        .organization { margin-bottom: 24px; text-align: center; }
        .organization-logo { display: block; width: auto; max-width: 150px; max-height: 72px; margin: 0 auto 12px; object-fit: contain; }
        .brand { margin: 0 0 8px; color: #172033; font-size: 18px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .organization-contact { margin: 4px 0 0; color: #64748b; font-size: 13px; overflow-wrap: anywhere; }
        .result { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .check { display: grid; width: 44px; height: 44px; place-items: center; border-radius: 50%; background: #dcfce7; color: #15803d; font-size: 24px; }
        h1 { margin: 0; font-size: 24px; }
        .subtitle { margin: 6px 0 0; color: #64748b; font-size: 14px; }
        dl { margin: 0; border-top: 1px solid #e2e8f0; }
        .row { display: flex; justify-content: space-between; gap: 16px; padding: 14px 0; border-bottom: 1px solid #e2e8f0; }
        dt { color: #64748b; }
        dd { margin: 0; text-align: right; font-weight: 700; }
        .notice { margin: 24px 0 0; color: #64748b; font-size: 13px; line-height: 1.5; }
        .verification-qr { display: block; width: 150px; height: 150px; margin: 24px auto 0; }
        @media (max-width: 420px) { .card { padding: 24px 20px; } h1 { font-size: 21px; } }
    </style>
</head>
<body>
    <main class="card">
        <header class="organization">
            <img class="organization-logo" src="{{ asset('assets/images/tncc-logo.png') }}" alt="TNCC-Kasumulu logo">
            <p class="brand">TNCC-Kasumulu</p>
            @if (!empty($organizationEmail))
                <p class="organization-contact">{{ $organizationEmail }}</p>
            @endif
            @if (!empty($organizationPhone))
                <p class="organization-contact">{{ $organizationPhone }}</p>
            @endif
        </header>
        <div class="result">
            <span class="check" aria-hidden="true">✓</span>
            <div>
                <h1>Payment verified</h1>
                <p class="subtitle">This payment reference matches our records.</p>
            </div>
        </div>
        <dl>
            <div class="row"><dt>Invoice reference</dt><dd>{{ $invoice->reference_number }}</dd></div>
            <div class="row"><dt>Invoice date</dt><dd>{{ optional($invoice->date)->format('d M, Y') ?? '—' }}</dd></div>
            <div class="row"><dt>Customer/member</dt><dd>{{ optional($invoice->customer)->name ?? '—' }}</dd></div>
            <div class="row"><dt>Phone</dt><dd>{{ optional($invoice->customer)->phone ?? '—' }}</dd></div>
            <div class="row"><dt>Location</dt><dd>{{ $invoice->location ?: '—' }}</dd></div>
            <div class="row"><dt>Plate number</dt><dd>{{ $invoice->plate_number ?: '—' }}</dd></div>
            <div class="row"><dt>Total amount</dt><dd>TZS {{ number_format((float) $invoice->total_amount, 2) }}</dd></div>
            <div class="row"><dt>Invoice status</dt><dd>{{ strtoupper($invoice->status ?? 'pending') }}</dd></div>
            <div class="row"><dt>Payment status</dt><dd>{{ strtoupper($invoice->payment->status ?? 'pending') }}</dd></div>
            @if (strtolower(optional($invoice->payment)->status ?? '') === 'completed' && $invoice->payment->transaction_reference)
                <div class="row"><dt>Payment transaction reference</dt><dd>{{ $invoice->payment->transaction_reference }}</dd></div>
            @endif
        </dl>
        <img class="verification-qr" src="{{ $qrCode }}" alt="QR code to verify invoice {{ $invoice->reference_number }}">
    </main>
</body>
</html>
