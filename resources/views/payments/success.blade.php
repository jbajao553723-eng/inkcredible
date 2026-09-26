<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment received</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite('resources/js/app.js')
    <style>
        body { background: #f6f7fb; min-height: 100vh; display: grid; place-items: center; }
        .result { max-width: 520px; margin: 24px; padding: 40px; background: #fff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,.06); text-align: center; }
        .icon { color: #198754; font-size: 48px; line-height: 1; }
        @include('partials.motion-styles')
    </style>
</head>
<body>
    <main class="result">
        <div class="icon" aria-hidden="true">&#10003;</div>
        @if($payment->status === 'approved')
            <h1 class="h3 mt-3">Payment confirmed</h1>
            <p class="text-secondary">
                Your payment of ₱{{ number_format($payment->amount, 2) }} was confirmed and has been deducted from your loan balance.
            </p>
        @else
            <h1 class="h3 mt-3">Payment is being confirmed</h1>
            <p class="text-secondary">
                PayMongo returned you safely. We are waiting for its signed confirmation; your dashboard will update once it arrives.
            </p>
        @endif
        <a class="btn btn-dark" href="{{ route('dashboard') }}">View dashboard</a>
        <a class="btn btn-outline-secondary" href="{{ route('payments.index') }}">Back to payments</a>
    </main>
</body>
</html>
