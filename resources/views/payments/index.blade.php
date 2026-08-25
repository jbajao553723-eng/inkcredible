<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Inkcredible - Payments</title>

<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
* { font-family: 'Space Grotesk', sans-serif; }

body {
    background:#f6f7fb;
    margin:0;
}

/* SIDEBAR */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 240px;
    height: 100vh;
    background: #111827;
    color: white;
    padding: 20px;
}

.sidebar h3 {
    font-size: 20px;
    margin-bottom: 25px;
}

.sidebar a {
    display: block;
    padding: 10px;
    color: #cbd5e1;
    text-decoration: none;
    border-radius: 10px;
    margin-bottom: 8px;
}

.sidebar a:hover {
    background: #1f2937;
    color: white;
}

/* MAIN */
.main {
    margin-left: 240px;
    padding: 20px;
}

/* TOPBAR */
.topbar {
    background: white;
    padding: 15px 20px;
    border-radius: 12px;
    display:flex;
    justify-content: space-between;
    align-items:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

/* CARD */
.card-box {
    background:white;
    padding:20px;
    border-radius:16px;
    margin-top:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.form-control {
    border-radius: 10px;
    margin-bottom: 12px;
}

.btn-submit {
    background:#4f46e5;
    color:white;
    width:100%;
    padding:10px;
    border:none;
    border-radius:10px;
    font-weight:600;
}

.btn-submit:hover {
    background:#4338ca;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>Inkcredible</h3>

    <a href="/dashboard">Dashboard</a>
    <a href="/loan/create">Request Loan</a>
    <a href="/payments">Payments</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div><b>Payments</b></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-danger btn-sm">Logout</button>
        </form>
    </div>

    <!-- SUCCESS -->
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- ERRORS -->
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- FORM -->
    <div class="card-box">

        <h5 class="mb-3">Submit Payment</h5>

        <form method="POST"
              action="{{ route('payments.store') }}"
              enctype="multipart/form-data">

            @csrf

            <!-- SELECT LOAN -->
            <label>Select Loan</label>

            <select name="loan_id" class="form-control" required>

                <option value="">-- Choose Loan --</option>

                @foreach($loans as $loan)

                    <option value="{{ $loan->id }}">

                        {{ $loan->loanType->display_name ?? $loan->loanType->name ?? 'Loan' }}

                        -

                        ₱{{ number_format($loan->amount, 2) }}

                        ({{ ucfirst($loan->status) }})

                    </option>

                @endforeach

            </select>

            <!-- AMOUNT -->
            <label>Payment Amount</label>

            <input type="number"
                   step="0.01"
                   name="amount"
                   class="form-control"
                   required>

            <!-- METHOD -->
            <label>Payment Method</label>

            <select name="method"
                    class="form-control"
                    required
                    id="payment-method">

                <option value="gcash">GCash</option>
                <option value="cash">Cash</option>

            </select>

            <!-- PROOF -->
            <div id="proof-section">

                <label>GCash Screenshot / Proof</label>

                <input type="file"
                       name="proof"
                       class="form-control"
                       accept="image/*">

            </div>

            <button type="submit" class="btn-submit mt-2">
                Submit Payment
            </button>

        </form>

    </div>

</div>

<script>

const paymentMethod = document.getElementById('payment-method');

paymentMethod.addEventListener('change', function () {

    const proofSection = document.getElementById('proof-section');

    const proofInput = document.querySelector('input[name="proof"]');

    if (this.value === 'gcash') {

        proofSection.style.display = 'block';
        proofInput.required = true;

    } else {

        proofSection.style.display = 'none';
        proofInput.required = false;

    }
});

window.onload = function () {

    paymentMethod.dispatchEvent(new Event('change'));

};

</script>

</body>
</html>