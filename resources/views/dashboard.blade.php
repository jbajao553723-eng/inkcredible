<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Inkcredible Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

*{
    font-family:'Space Grotesk',sans-serif;
}

body{
    background:#f6f7fb;
    margin:0;
}

/* SIDEBAR */

.sidebar{
    position:fixed;
    top:0;
    left:0;
    width:240px;
    height:100vh;
    background:#111827;
    color:white;
    padding:20px;
}

.sidebar h3{
    margin-bottom:25px;
}

.sidebar a{
    display:block;
    padding:12px;
    color:#cbd5e1;
    text-decoration:none;
    border-radius:10px;
    margin-bottom:8px;
    transition:0.2s;
}

.sidebar a:hover{
    background:#1f2937;
    color:white;
}

/* MAIN */

.main{
    margin-left:240px;
    padding:20px;
}

/* TOPBAR */

.topbar{
    background:white;
    padding:15px 20px;
    border-radius:16px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

/* CARDS */

.cards{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:15px;
    margin-top:20px;
}

.card-box{
    background:white;
    padding:20px;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.label{
    font-size:13px;
    color:#6b7280;
}

.value{
    font-size:24px;
    font-weight:700;
    margin-top:5px;
}

/* TABLE BOX */

.table-box{
    background:white;
    padding:20px;
    border-radius:16px;
    margin-top:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.table th{
    font-size:14px;
}

.table td{
    vertical-align:middle;
}

/* BADGES */

.badge{
    padding:8px 12px;
    border-radius:20px;
    font-size:12px;
}

.progress{
    height:8px;
    border-radius:20px;
}

/* EMPTY */

.empty{
    color:#9ca3af;
    text-align:center;
    padding:20px;
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

    <div>
        <h5 class="mb-0">Client Dashboard</h5>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-danger btn-sm">
            Logout
        </button>
    </form>

</div>

@php

$loans = $loans ?? collect();
$pendingPayments = $pendingPayments ?? collect();

/*
|--------------------------------------------------------------------------
| NORMALIZE STATUS
|--------------------------------------------------------------------------
*/

$loans = $loans->map(function ($loan) {

    $loan->status = strtolower(trim($loan->status ?? 'pending'));

    return $loan;
});

/*
|--------------------------------------------------------------------------
| ACTIVE LOAN
|--------------------------------------------------------------------------
*/

$activeLoan = $loans
    ->where('status', 'approved')
    ->sortByDesc('created_at')
    ->first();

/*
|--------------------------------------------------------------------------
| TOTAL APPROVED LOANS
|--------------------------------------------------------------------------
*/

$totalBorrowed = $loans
    ->filter(fn($loan) => $loan->status === 'approved')
    ->sum(fn($loan) => $loan->amount ?? 0);

/*
|--------------------------------------------------------------------------
| TOTAL REMAINING
|--------------------------------------------------------------------------
*/

$totalRemaining = $loans
    ->filter(fn($loan) => $loan->status === 'approved')
    ->sum(function($loan){

        $penalty = $loan->penalty_amount ?? 0;

        $total = ($loan->total_payable ?? 0) + $penalty;

        $paid = $loan->paid_amount ?? 0;

        return max($total - $paid,0);

    });

/*
|--------------------------------------------------------------------------
| PAYMENT HISTORY
|--------------------------------------------------------------------------
*/

$allPayments = collect();

foreach($loans as $loan){

    foreach($loan->payments ?? [] as $payment){

        $payment->loan_ref = $loan;

        $allPayments->push($payment);

    }

}

$allPayments = $allPayments->sortByDesc('created_at');

@endphp

<!-- CARDS -->
<div class="cards">

    <!-- TOTAL LOANS -->
    <div class="card-box">

        <div class="label">
            Total Loans
        </div>

        <div class="value">
            {{ $loans->count() }}
        </div>

    </div>

    <!-- TOTAL BORROWED -->
    <div class="card-box">

        <div class="label">
            Total Borrowed
        </div>

        <div class="value">
            ₱{{ number_format($totalBorrowed,2) }}
        </div>

    </div>

    <!-- TOTAL REMAINING -->
    <div class="card-box">

        <div class="label">
            Remaining Balance
        </div>

        <div class="value">
            ₱{{ number_format($totalRemaining,2) }}
        </div>

    </div>

    <!-- NEXT PAYMENT -->
    <div class="card-box">

        <div class="label">
            Next Payment
        </div>

        @if($activeLoan && $activeLoan->next_payment_date)

            <div class="value text-danger">
                {{ \Carbon\Carbon::parse($activeLoan->next_payment_date)->format('M d, Y') }}
            </div>

        @else

            <div class="value text-muted">
                None
            </div>

        @endif

    </div>

</div>

<!-- LOANS TABLE -->
<div class="table-box">

<h5 class="mb-3">
    My Loans
</h5>

<table class="table table-hover">

<thead>

<tr>

    <th>Loan Type</th>
    <th>Amount</th>
    <th>Total Payable</th>
    <th>Paid</th>
    <th>Remaining</th>
    <th>Progress</th>
    <th>Status</th>

</tr>

</thead>

<tbody>

@forelse($loans as $loan)

@php

    $penalty = $loan->penalty_amount ?? 0;

    $paid = $loan->paid_amount ?? 0;

    $total = ($loan->total_payable ?? 0) + $penalty;

    $remaining = max($total - $paid,0);

    $progress = 0;

    if($total > 0){

        $progress = ($paid / $total) * 100;

    }

@endphp

<tr>

    <!-- TYPE -->
    <td>

        {{ optional($loan->loanType)->display_name
            ?? optional($loan->loanType)->name
            ?? 'No Type' }}

    </td>

    <!-- AMOUNT -->
    <td>
        ₱{{ number_format($loan->amount ?? 0,2) }}
    </td>

    <!-- TOTAL -->
    <td>
        ₱{{ number_format($total,2) }}
    </td>

    <!-- PAID -->
    <td>
        ₱{{ number_format($paid,2) }}
    </td>

    <!-- REMAINING -->
    <td>

        @if($remaining <= 0)

            <span class="badge bg-success">
                Fully Paid
            </span>

        @else

            ₱{{ number_format($remaining,2) }}

        @endif

    </td>

    <!-- PROGRESS -->
    <td style="width:180px;">

        <div class="progress">

            <div class="progress-bar"
                 role="progressbar"
                 style="width: {{ min($progress,100) }}%">

            </div>

        </div>

        <small>
            {{ number_format(min($progress,100),0) }}%
        </small>

    </td>

    <!-- STATUS -->
    <td>

        @switch($loan->status)

            @case('approved')

                <span class="badge bg-success">
                    Approved
                </span>

                @break

            @case('pending')

                <span class="badge bg-warning text-dark">
                    Pending
                </span>

                @break

            @case('rejected')

                <span class="badge bg-danger">
                    Rejected
                </span>

                @break

            @case('paid')

                <span class="badge bg-primary">
                    Paid
                </span>

                @break

            @default

                <span class="badge bg-secondary">
                    Unknown
                </span>

        @endswitch

    </td>

</tr>

@empty

<tr>

    <td colspan="7" class="empty">
        No loans found
    </td>

</tr>

@endforelse

</tbody>

</table>

</div>

<!-- PAYMENT HISTORY -->
<div class="table-box">

<h5 class="mb-3">
    Payment History
</h5>

<table class="table table-hover">

<thead>

<tr>

    <th>Loan Type</th>
    <th>Amount</th>
    <th>Method</th>
    <th>Status</th>
    <th>Date</th>

</tr>

</thead>

<tbody>

@forelse($allPayments as $payment)

<tr>

    <!-- TYPE -->
    <td>

        {{ optional($payment->loan_ref->loanType)->display_name
            ?? optional($payment->loan_ref->loanType)->name
            ?? 'Loan' }}

    </td>

    <!-- AMOUNT -->
    <td>
        ₱{{ number_format($payment->amount ?? 0,2) }}
    </td>

    <!-- METHOD -->
    <td>
        {{ strtoupper($payment->method ?? 'N/A') }}
    </td>

    <!-- STATUS -->
    <td>

        @if($payment->status == 'approved')

            <span class="badge bg-success">
                Approved
            </span>

        @elseif($payment->status == 'pending')

            <span class="badge bg-warning text-dark">
                Pending
            </span>

        @elseif($payment->status == 'rejected')

            <span class="badge bg-danger">
                Rejected
            </span>

        @else

            <span class="badge bg-secondary">
                Unknown
            </span>

        @endif

    </td>

    <!-- DATE -->
    <td>

        {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y h:i A') }}

    </td>

</tr>

@empty

<tr>

    <td colspan="5" class="empty">
        No payment history yet
    </td>

</tr>

@endforelse

</tbody>

</table>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>