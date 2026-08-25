<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inkcredible - Loan Request</title>

    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Space Grotesk', sans-serif;
            box-sizing: border-box;
        }

        body {
            background: #f6f7fb;
            padding: 30px;
        }

        .container-box {
            max-width: 900px;
            margin: auto;
        }

        .header {
            margin-bottom: 20px;
        }

        .title {
            font-size: 28px;
            font-weight: 700;
        }

        .subtitle {
            color: #6b7280;
            font-size: 14px;
        }

        .loan-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }

        .loan-card {
            background: white;
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            border: 2px solid transparent;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: 0.25s;
        }

        .loan-card:hover {
            transform: translateY(-5px);
        }

        .loan-card.active {
            border-color: #6366f1;
            background: #eef2ff;
        }

        .loan-title {
            font-size: 18px;
            font-weight: 700;
        }

        .loan-desc {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }

        .form-box {
            background: white;
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.06);
        }

        .form-control {
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99,102,241,0.12);
        }

        .hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: white;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(99,102,241,0.25);
        }

        @media (max-width: 768px) {
            .loan-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="container-box">

    <div class="header">
        <div class="title">Request Loan</div>
        <div class="subtitle">Choose loan type and submit your application</div>
    </div>

    <!-- 🔥 ERROR DISPLAY (IMPORTANT FIX) -->
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('loan.store') }}" enctype="multipart/form-data">
        @csrf   

        <!-- LOAN TYPE CARDS -->
        <div class="loan-grid">

            <div class="loan-card" onclick="selectLoan('arawan', event)">
                <div class="loan-title">Arawan</div>
                <div class="loan-desc">Max ₱2,000 • Daily payment</div>
            </div>

            <div class="loan-card" onclick="selectLoan('weekly', event)">
                <div class="loan-title">Weekly</div>
                <div class="loan-desc">₱10K - ₱20K • 1–3 weeks</div>
            </div>

            <div class="loan-card" onclick="selectLoan('emergency', event)">
                <div class="loan-title">Emergency</div>
                <div class="loan-desc">12% interest • Fast approval</div>
            </div>

        </div>

        <!-- ✅ FIXED: loan_type (WAS type BEFORE) -->
        <input type="hidden" name="loan_type" id="loan_type" required>

        <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter loan amount" required>

        <div class="hint" id="hint">Select a loan type to see rules</div>

        <!-- ID UPLOAD -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Government Issued ID (Required)</label>

            <input type="file"
                   name="government_id"
                   class="form-control"
                   accept="image/*,application/pdf"
                   required>

            <small class="text-muted">
                Upload valid ID (National ID, Driver’s License, Passport)
            </small>
        </div>

        <textarea name="purpose" class="form-control" placeholder="Loan purpose (optional)"></textarea>

        <button type="submit" class="btn-submit">Submit Loan Request</button>

    </form>

</div>

<script>
function selectLoan(type, event) {
    document.getElementById("loan_type").value = type;

    document.querySelectorAll(".loan-card").forEach(c => c.classList.remove("active"));
    event.currentTarget.classList.add("active");

    let hint = document.getElementById("hint");

    if (type === "arawan") {
        hint.innerText = "Max ₱2,000 • Daily payment • 10% interest";
    }

    if (type === "weekly") {
        hint.innerText = "₱10,000 - ₱20,000 • 1–3 weeks • 8% interest";
    }

    if (type === "emergency") {
        hint.innerText = "Any amount • 12% interest • Fast approval";
    }
}
</script>

</body>
</html>