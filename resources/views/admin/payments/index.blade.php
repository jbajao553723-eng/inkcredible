<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Payment Management</title>

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

        .topbar{
            background:white;
            padding:20px 35px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow:0 8px 20px rgba(0,0,0,0.05);
        }

        .title{
            font-size:22px;
            font-weight:700;
        }

        .subtitle{
            font-size:13px;
            color:#6b7280;
        }

        .btn-back{
            background:#2563eb;
            color:white;
            text-decoration:none;
            padding:10px 18px;
            border-radius:10px;
            font-size:14px;
            font-weight:600;
        }

        .btn-back:hover{
            background:#1d4ed8;
            color:white;
        }

        .container-box{
            padding:30px;
        }

        .card-box{
            background:white;
            border-radius:20px;
            padding:25px;
            box-shadow:0 12px 30px rgba(0,0,0,0.05);
            overflow-x:auto;
        }

        table{
            width:100%;
        }

        th{
            color:#6b7280;
            font-size:13px;
            font-weight:600;
        }

        td{
            vertical-align:middle;
            font-size:14px;
        }

        .client-name{
            font-weight:700;
        }

        .client-email{
            font-size:12px;
            color:#6b7280;
        }

        .badge-pending{
            background:#fef3c7;
            color:#92400e;
            padding:7px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:600;
        }

        .badge-approved{
            background:#dcfce7;
            color:#166534;
            padding:7px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:600;
        }

        .badge-rejected{
            background:#fee2e2;
            color:#991b1b;
            padding:7px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:600;
        }

        .btn-view{
            background:#2563eb;
            color:white;
            border:none;
            padding:7px 12px;
            border-radius:10px;
            font-size:12px;
            text-decoration:none;
            display:inline-block;
            margin-top:6px;
        }

        .btn-approve{
            background:#16a34a;
            color:white;
            border:none;
            padding:8px 12px;
            border-radius:10px;
            font-size:12px;
            font-weight:600;
        }

        .btn-reject{
            background:#dc2626;
            color:white;
            border:none;
            padding:8px 12px;
            border-radius:10px;
            font-size:12px;
            font-weight:600;
        }

        .proof-preview{
            width:90px;
            height:60px;
            object-fit:cover;
            border-radius:10px;
            border:1px solid #ddd;
        }

        .action-group{
            display:flex;
            gap:8px;
            flex-wrap:wrap;
        }

    </style>
</head>

<body>

    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <div class="title">
                Payment Management
            </div>

            <div class="subtitle">
                Review and process client payment submissions
            </div>

        </div>

        <a href="/admin/dashboard" class="btn-back">
            ← Back Dashboard
        </a>

    </div>

    <!-- CONTENT -->

    <div class="container-box">

        <div class="card-box">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>Client</th>
                        <th>Loan</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Proof</th>
                        <th>Date</th>
                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($payments as $payment)

                    <tr>

                        <!-- CLIENT -->

                        <td>

                            <div class="client-name">
                                {{ $payment->loan->user->name ?? 'Unknown User' }}
                            </div>

                            <div class="client-email">
                                {{ $payment->loan->user->email ?? 'No Email' }}
                            </div>

                        </td>

                        <!-- LOAN -->

                        <td>
                            {{ ucfirst($payment->loan->loan_type ?? 'N/A') }} - ₱{{ number_format($payment->loan->amount, 2) }}
                        </td>

                        <!-- AMOUNT -->

                        <td>
                            ₱{{ number_format($payment->amount, 2) }}
                        </td>

                        <!-- METHOD -->

                        <td>
                            {{ ucfirst($payment->method) }}
                        </td>

                        <!-- STATUS -->

                        <td>

                            @if(strtolower($payment->status) == 'pending')

                                <span class="badge-pending">
                                    Pending
                                </span>

                            @elseif(strtolower($payment->status) == 'approved')

                                <span class="badge-approved">
                                    Approved
                                </span>

                            @else

                                <span class="badge-rejected">
                                    Rejected
                                </span>

                            @endif

                        </td>

                        <!-- PROOF -->

                        <td>

                            @if($payment->proof)

                                <a href="{{ asset('storage/'.$payment->proof) }}"
                                   target="_blank">

                                    <img src="{{ asset('storage/'.$payment->proof) }}"
                                         class="proof-preview">

                                </a>

                                <br>

                                <a href="{{ asset('storage/'.$payment->proof) }}"
                                   target="_blank"
                                   class="btn-view">

                                    View Proof

                                </a>

                            @else

                                <span class="text-muted">
                                    No Proof
                                </span>

                            @endif

                        </td>

                        <!-- DATE -->

                        <td>
                            {{ $payment->created_at->format('M d, Y') }}
                        </td>

                        <!-- ACTIONS -->

                        <td>

                            @if(strtolower($payment->status) == 'pending')

                                <div class="action-group">

                                    <!-- VIEW -->

                                    <a href="{{ route('admin.payment.show', $payment->id) }}"
                                       class="btn-view">

                                        View

                                    </a>

                                    <!-- APPROVE -->

                                    <form method="POST"
                                          action="{{ route('admin.payment.approve', $payment->id) }}">

                                        @csrf

                                        <button type="submit"
                                                class="btn-approve">

                                            Approve

                                        </button>

                                    </form>

                                    <!-- REJECT -->

                                    <form method="POST"
                                          action="{{ route('admin.payment.reject', $payment->id) }}">

                                        @csrf

                                        <button type="submit"
                                                class="btn-reject">

                                            Reject

                                        </button>

                                    </form>

                                </div>

                            @else

                                <a href="{{ route('admin.payment.show', $payment->id) }}"
                                   class="btn-view">

                                    View Details

                                </a>

                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="8" class="text-center text-muted py-4">
                            No payments found
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</body>
</html>