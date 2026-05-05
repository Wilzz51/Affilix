<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #16a34a; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .body { padding: 32px 40px; color: #374151; }
        .amount { font-size: 28px; font-weight: bold; color: #16a34a; margin: 16px 0; }
        .detail { background: #f9fafb; border-radius: 6px; padding: 16px; margin: 20px 0; font-size: 14px; }
        .detail p { margin: 6px 0; }
        .footer { padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ __('Affilix::affiliation.emails.commission_approved_subject') }}</h1>
    </div>
    <div class="body">
        <p>{{ __('Bonjour') }} <strong>{{ $commission->affiliate->customer->firstname ?? $commission->affiliate->customer->name }}</strong>,</p>
        <p>{{ __('Affilix::affiliation.emails.commission_approved_intro') }}</p>
        <div class="amount">{{ number_format($commission->amount, 2) }} {{ setting('currency_symbol', '€') }}</div>
        <div class="detail">
            <p><strong>{{ __('Affilix::affiliation.invoice') }} :</strong> #{{ $commission->invoice_id }}</p>
            <p><strong>{{ __('Affilix::affiliation.stats.commission_rate') }} :</strong> {{ $commission->commission_rate }}%</p>
            <p><strong>{{ __('Affilix::affiliation.date') }} :</strong> {{ $commission->approved_at->format('d/m/Y') }}</p>
        </div>
        <p>{{ __('Affilix::affiliation.emails.commission_approved_detail') }}</p>
    </div>
    <div class="footer">
        {{ setting('app_name', config('app.name')) }}
    </div>
</div>
</body>
</html>
