<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Inkcredible Admin Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

:root{
    --blue:#2563eb;
    --green:#22c55e;
    --red:#ef4444;
    --yellow:#f59e0b;
    --purple:#7c3aed;

    --bg:#f3f4f6;
    --card:#ffffff;
    --border:#e5e7eb;

    --text:#111827;
    --muted:#6b7280;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}

body{
    background:var(--bg);
    color:var(--text);
}

/* SIDEBAR */

.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    left:0;
    top:0;
    background:#111827;
    padding:25px 18px;
    overflow-y:auto;
}

.brand{
    color:white;
    font-size:22px;
    font-weight:800;
    margin-bottom:30px;
}

.nav-item{
    display:block;
    text-decoration:none;
    color:#cbd5e1;
    padding:12px 14px;
    border-radius:12px;
    margin-bottom:8px;
    transition:.2s;
    font-size:14px;
    font-weight:500;
}

.nav-item:hover{
    background:#1f2937;
    color:white;
}

.nav-item.active{
    background:var(--blue);
    color:white;
}

/* MAIN */

.main{
    margin-left:250px;
    padding:25px;
}

/* HEADER */

.header{
    background:var(--card);
    border-radius:18px;
    padding:18px 22px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.04);
}

.admin-title{
    font-size:24px;
    font-weight:800;
}

.admin-sub{
    font-size:13px;
    color:var(--muted);
}

.logout-btn{
    background:var(--red);
    color:white;
    border:none;
    border-radius:10px;
    padding:10px 15px;
    font-size:13px;
    font-weight:600;
}

/* CARDS */

.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:18px;
    margin-top:20px;
}

.stat-card{
    background:var(--card);
    padding:20px;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,0.04);
}

.stat-label{
    font-size:13px;
    color:var(--muted);
    margin-bottom:10px;
}

.stat-value{
    font-size:28px;
    font-weight:800;
}

/* TABS */

.tabs{
    display:flex;
    gap:10px;
    margin-top:20px;
    flex-wrap:wrap;
}

.tab-btn{
    border:none;
    background:white;
    padding:10px 18px;
    border-radius:12px;
    font-weight:600;
    font-size:14px;
    cursor:pointer;
}

.tab-btn.active{
    background:var(--blue);
    color:white;
}

/* CONTENT */

.tab-content{
    background:white;
    margin-top:20px;
    padding:20px;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,0.04);
}

/* TABLE */

.table{
    vertical-align:middle;
}

.table thead th{
    font-size:13px;
    color:var(--muted);
    font-weight:700;
}

.table tbody td{
    font-size:14px;
}

/* STATUS */

.status{
    padding:6px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
}

.pending{
    background:rgba(245,158,11,.12);
    color:var(--yellow);
}

.approved{
    background:rgba(34,197,94,.12);
    color:var(--green);
}

.rejected{
    background:rgba(239,68,68,.12);
    color:var(--red);
}

.paid{
    background:rgba(37,99,235,.12);
    color:var(--blue);
}

/* CHART */

.chart-box{
    background:white;
    border-radius:16px;
    padding:20px;
    margin-top:20px;
}

/* QUICK ACTIONS */

.quick-actions{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:15px;
    margin-top:20px;
}

.quick-card{
    background:white;
    padding:20px;
    border-radius:18px;
    text-decoration:none;
    color:var(--text);
    box-shadow:0 10px 25px rgba(0,0,0,0.04);
    transition:.2s;
}

.quick-card:hover{
    transform:translateY(-3px);
}

.quick-title{
    font-weight:700;
    margin-top:10px;
}

.quick-sub{
    font-size:13px;
    color:var(--muted);
}

/* MOBILE */

@media(max-width:1200px){

    .stats-grid{
        grid-template-columns:repeat(2,1fr);
    }

    .quick-actions{
        grid-template-columns:1fr;
    }
}

@media(max-width:768px){

    .sidebar{
        display:none;
    }

    .main{
        margin-left:0;
    }

    .stats-grid{
        grid-template-columns:1fr;
    }
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="brand">
        Inkcredible
    </div>

    <a href="{{ route('admin.dashboard') }}"
       class="nav-item active">
       Dashboard
    </a>

    <a href="{{ route('admin.loans') }}"
       class="nav-item">
       Loan Requests
    </a>

    <a href="{{ route('admin.clients') }}"
       class="nav-item">
       Clients
    </a>

    <a href="{{ route('admin.payments.index') }}"
       class="nav-item">
       Payments
    </a>

</div>

<!-- MAIN -->
<div class="main">

<!-- HEADER -->
<div class="header">

    <div>
        <div class="admin-title">
            Admin Dashboard
        </div>

        <div class="admin-sub">
            Manage loans, payments, clients and analytics
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="logout-btn">
            Logout
        </button>
    </form>

</div>

@php

$totalLoans = $recentLoans->count();

$approvedLoans = $recentLoans
    ->where('status','approved')
    ->count();

$pendingLoans = $recentLoans
    ->where('status','pending')
    ->count();

$totalReleased = $recentLoans
    ->where('status','approved')
    ->sum('amount');

$totalCollected = $recentLoans
    ->sum('paid_amount');

$totalProfit = $recentLoans
    ->where('status','approved')
    ->sum(function($loan){

        $interest = 0;

        $type = strtolower($loan->loanType->name ?? '');

        if($type == 'arawan'){
            $interest = 0.10;
        }
        elseif($type == 'weekly'){
            $interest = 0.08;
        }
        elseif($type == 'emergency'){
            $interest = 0.12;
        }

        return ($loan->amount ?? 0) * $interest;
    });

@endphp

<!-- STATS -->
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-label">
            Total Loans
        </div>

        <div class="stat-value">
            {{ $totalLoans }}
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">
            Approved Loans
        </div>

        <div class="stat-value">
            {{ $approvedLoans }}
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">
            Total Released
        </div>

        <div class="stat-value">
            ₱{{ number_format($totalReleased,2) }}
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">
            Total Profit
        </div>

        <div class="stat-value">
            ₱{{ number_format($totalProfit,2) }}
        </div>
    </div>

</div>

<!-- QUICK ACTIONS -->
<div class="quick-actions">

    <a href="{{ route('admin.loans') }}" class="quick-card">
        <div style="font-size:30px;">📄</div>

        <div class="quick-title">
            Manage Loans
        </div>

        <div class="quick-sub">
            Approve or reject loan requests
        </div>
    </a>

    <a href="{{ route('admin.payments.index') }}" class="quick-card">
        <div style="font-size:30px;">💳</div>

        <div class="quick-title">
            Payment Requests
        </div>

        <div class="quick-sub">
            Verify submitted payments
        </div>
    </a>

    <a href="{{ route('admin.clients') }}" class="quick-card">
        <div style="font-size:30px;">👥</div>

        <div class="quick-title">
            Client Management
        </div>

        <div class="quick-sub">
            View registered clients
        </div>
    </a>

</div>

<!-- TABS -->
<div class="tabs">

    <button class="tab-btn active"
        onclick="openTab(event,'loans')">
        Loans
    </button>

    <button class="tab-btn"
        onclick="openTab(event,'analytics')">
        Analytics
    </button>

</div>

<!-- LOANS TAB -->
<div id="loans" class="tab-content">

<h5 class="mb-4">
    Recent Loans
</h5>

<div class="table-responsive">

<table class="table align-middle">

<thead>
<tr>
    <th>Client</th>
    <th>Loan Type</th>
    <th>Amount</th>
    <th>Total Payable</th>
    <th>Paid</th>
    <th>Remaining</th>
    <th>Status</th>
</tr>
</thead>

<tbody>

@forelse($recentLoans as $loan)

@php

$status = strtolower($loan->status ?? 'pending');

$remaining =
    ($loan->total_payable ?? 0)
    -
    ($loan->paid_amount ?? 0);

@endphp

<tr>

    <td>
        <strong>
            {{ $loan->user->name ?? 'Unknown User' }}
        </strong>
    </td>

    <td>
        {{ $loan->loanType->display_name
            ?? $loan->loanType->name
            ?? 'N/A' }}
    </td>

    <td>
        ₱{{ number_format($loan->amount ?? 0,2) }}
    </td>

    <td>
        ₱{{ number_format($loan->total_payable ?? 0,2) }}
    </td>

    <td>
        ₱{{ number_format($loan->paid_amount ?? 0,2) }}
    </td>

    <td>
        ₱{{ number_format(max($remaining,0),2) }}
    </td>

    <td>

        @if($status == 'approved')
            <span class="status approved">
                Approved
            </span>

        @elseif($status == 'pending')
            <span class="status pending">
                Pending
            </span>

        @elseif($status == 'paid')
            <span class="status paid">
                Paid
            </span>

        @else
            <span class="status rejected">
                Rejected
            </span>
        @endif

    </td>

</tr>

@empty

<tr>
    <td colspan="7" class="text-center text-muted">
        No loans found
    </td>
</tr>

@endforelse

</tbody>

</table>

</div>

</div>

<!-- ANALYTICS TAB -->
<div id="analytics"
     class="tab-content"
     style="display:none;">

<h5 class="mb-4">
    Loan Analytics
</h5>

<div class="chart-box">
    <canvas id="loanChart"></canvas>
</div>

<div class="chart-box">
    <canvas id="statusChart"></canvas>
</div>

</div>

</div>

<script>

function openTab(event, tab){

    document.querySelectorAll('.tab-content')
        .forEach(el => el.style.display='none');

    document.querySelectorAll('.tab-btn')
        .forEach(el => el.classList.remove('active'));

    document.getElementById(tab).style.display='block';

    event.target.classList.add('active');
}

/* LOAN TYPE CHART */

new Chart(document.getElementById('loanChart'), {

    type:'bar',

    data:{

        labels:['Arawan','Weekly','Emergency'],

        datasets:[{

            label:'Loan Count',

            data:[

                {{ $recentLoans->filter(fn($l)=>strtolower($l->loanType->name ?? '') == 'arawan')->count() }},

                {{ $recentLoans->filter(fn($l)=>strtolower($l->loanType->name ?? '') == 'weekly')->count() }},

                {{ $recentLoans->filter(fn($l)=>strtolower($l->loanType->name ?? '') == 'emergency')->count() }}

            ]

        }]
    }
});

/* STATUS CHART */

new Chart(document.getElementById('statusChart'), {

    type:'doughnut',

    data:{

        labels:['Approved','Pending','Rejected'],

        datasets:[{

            data:[

                {{ $recentLoans->where('status','approved')->count() }},

                {{ $recentLoans->where('status','pending')->count() }},

                {{ $recentLoans->where('status','rejected')->count() }}

            ]

        }]
    }
});

</script>

</body>
</html>