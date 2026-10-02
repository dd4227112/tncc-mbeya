<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Verification - {{ $invoice->reference_number }}</title>
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
        .brand { margin: 0 0 24px; color: #475569; font-size: 14px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .result { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .check { display: grid; width: 44px; height: 44px; place-items: center; border-radius: 50%; background: #dcfce7; color: #15803d; font-size: 24px; }
        h1 { margin: 0; font-size: 24px; }
        .subtitle { margin: 6px 0 0; color: #64748b; font-size: 14px; }
        dl { margin: 0; border-top: 1px solid #e2e8f0; }
        .row { display: flex; justify-content: space-between; gap: 16px; padding: 14px 0; border-bottom: 1px solid #e2e8f0; }
        dt { color: #64748b; }
        dd { margin: 0; text-align: right; font-weight: 700; }
        .notice { margin: 24px 0 0; color: #64748b; font-size: 13px; line-height: 1.5; }
        @media (max-width: 420px) { .card { padding: 24px 20px; } h1 { font-size: 21px; } }
    </style>
</head>
<body>
    <main class="card">
        <p class="brand">TNCC-Kasumulu</p>
        <div class="result">
            <span class="check" aria-hidden="true">✓</span>
            <div>
                <h1>Invoice verified</h1>
                <p class="subtitle">This invoice reference matches our records.</p>
            </div>
        </div>
        <dl>
            <div class="row"><dt>Invoice reference</dt><dd>{{ $invoice->reference_number }}</dd></div>
            <div class="row"><dt>Invoice date</dt><dd>{{ optional($invoice->date)->format('d M, Y') ?? '—' }}</dd></div>
            <div class="row"><dt>Total amount</dt><dd>TZS {{ number_format((float) $invoice->total_amount, 2) }}</dd></div>
            <div class="row"><dt>Invoice status</dt><dd>{{ strtoupper($invoice->status ?? 'pending') }}</dd></div>
            <div class="row"><dt>Payment status</dt><dd>{{ strtoupper($invoice->payment->status ?? 'pending') }}</dd></div>
        </dl>
        <p class="notice">This page confirms the invoice reference, amount, and current status. It does not replace proof of payment.</p>
    </main>
</body>
</html>
