<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Payment Details</title>

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
        }

        .detail-row{
            display:flex;
            justify-content:space-between;
            padding:15px 0;
            border-bottom:1px solid #f3f4f6;
        }

        .detail-label{
            font-weight:600;
            color:#374151;
        }

        .detail-value{
            color:#6b7280;
        }

        .proof-image{
            max-width:100%;
            height:auto;
            border-radius:10px;
            border:1px solid #ddd;
            margin-top:10px;
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

        .btn-approve{
            background:#16a34a;
            color:white;
            border:none;
            padding:10px 20px;
            border-radius:10px;
            font-size:14px;
            font-weight:600;
            text-decoration:none;
            display:inline-block;
        }

        .btn-reject{
            background:#dc2626;
            color:white;
            border:none;
            padding:10px 20px;
            border-radius:10px;
            font-size:14px;
            font-weight:600;
            text-decoration:none;
            display:inline-block;
        }

        .action-group{
            display:flex;
            gap:15px;
            justify-content:center;
            margin-top:30px;
        }

    </style>
</head>

<body>

    <!-- TOPBAR -->

    <div class="topbar">

        <div>

            <div class="title">
                Payment Details
            </div>

            <div class="subtitle">
                Review payment submission details
            </div>

        </div>

        <a href="{{ route('admin.payments.index') }}" class="btn-back">
            ← Back to Payments
        </a>

    </div>

    <!-- CONTENT -->

    <div class="container-box">

        <div class="card-box">

            <h5 class="mb-4">Payment Information</h5>

            <!-- PAYMENT DETAILS -->

            <div class="detail-row">
                <span class="detail-label">Payment ID:</span>
                <span class="detail-value">#{{ $payment->id }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Client Name:</span>
                <span class="detail-value">{{ $payment->loan->user->name ?? 'Unknown User' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Client Email:</span>
                <span class="detail-value">{{ $payment->loan->user->email ?? 'No Email' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Loan Type:</span>
                <span class="detail-value">{{ ucfirst($payment->loan->loan_type ?? 'N/A') }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Loan Amount:</span>
                <span class="detail-value">₱{{ number_format($payment->loan->amount, 2) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Payment Amount:</span>
                <span class="detail-value">₱{{ number_format($payment->amount, 2) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{{ ucfirst($payment->method) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">

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

                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Submitted Date:</span>
                <span class="detail-value">{{ $payment->created_at->format('F d, Y \a\t g:i A') }}</span>
            </div>

            <!-- PENALTY INFORMATION -->
            @php
                $loan = $payment->loan;
                $overdueDays = $loan->getOverdueDays();
                $penaltyAmount = $loan->penalty_amount ?? 0;
            @endphp

            @if($overdueDays > 0)
            <div class="detail-row" style="background: #fee2e2; padding: 15px; border-radius: 8px; margin-top: 15px;">
                <div style="width: 100%;">
                    <p style="margin: 0; color: #991b1b; font-weight: 600;">⚠️ LOAN IS OVERDUE - PENALTY APPLIED</p>
                    <p style="margin: 5px 0 0 0; color: #991b1b; font-size: 14px;">
                        Overdue Days: {{ $overdueDays }} | Daily Penalty: {{ $loan->penalty_percentage }}% | Penalty Amount: ₱{{ number_format($penaltyAmount, 2) }}
                    </p>
                </div>
            </div>
            @endif

            <!-- PROOF IMAGE -->

            @if($payment->proof)

                <div class="mt-4">

                    <h6 class="detail-label mb-3">Payment Proof:</h6>

                    <a href="{{ asset('storage/'.$payment->proof) }}" target="_blank">

                        <img src="{{ asset('storage/'.$payment->proof) }}"
                             class="proof-image"
                             alt="Payment Proof">

                    </a>

                    <br>

                    <a href="{{ asset('storage/'.$payment->proof) }}"
                       target="_blank"
                       class="btn btn-primary btn-sm mt-2">

                        View Full Size

                    </a>

                </div>

            @endif

            <!-- ACTIONS -->

            @if(strtolower($payment->status) == 'pending')

                <div class="action-group">

                    <form method="POST" action="{{ route('admin.payment.approve', $payment->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-approve">
                            ✓ Approve Payment
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.payment.reject', $payment->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-reject">
                            ✗ Reject Payment
                        </button>
                    </form>

                </div>

            @endif

        </div>

    </div>

</body>
</html>