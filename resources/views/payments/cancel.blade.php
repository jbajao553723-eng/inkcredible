<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment not completed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite('resources/js/app.js')
    <style>
        body { background: #f6f7fb; min-height: 100vh; display: grid; place-items: center; }
        .result { max-width: 520px; margin: 24px; padding: 40px; background: #fff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,.06); text-align: center; }
        .icon { color: #6c757d; font-size: 48px; line-height: 1; }
        @include('partials.motion-styles')
    </style>
</head>
<body>
    <main class="result">
        <div class="icon" aria-hidden="true">&#8212;</div>
        <h1 class="h3 mt-3">Payment not completed</h1>
        <p class="text-secondary">The authorization was cancelled or rejected, so no payment was applied to your loan. Start a new payment to receive a fresh secure checkout session.</p>
        <a class="btn btn-dark" href="{{ route('payments.index') }}">Try another payment</a>
    </main>
    <script>
        if (window.opener && !window.opener.closed) {
            window.opener.location.replace(window.location.href);
            window.close();
        }
    </script>
</body>
</html>
