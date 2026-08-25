<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inkcredible - Register</title>

    <!-- SAME FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Space Grotesk', sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f6f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            width: 100%;
            max-width: 950px;
            display: flex;
            border-radius: 24px;
            overflow: hidden;
            background: white;
            box-shadow: 0 25px 70px rgba(0,0,0,0.10);
        }

        /* LEFT */
        .left {
            flex: 1;
            background: linear-gradient(135deg, #065f46, #0f172a);
            color: white;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .brand {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .subtitle {
            margin-top: 12px;
            font-size: 14px;
            opacity: 0.8;
        }

        /* RIGHT */
        .right {
            flex: 1.2;
            padding: 55px;
        }

        .title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .desc {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 22px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 14px;
        }

        .form-control:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 4px rgba(34,197,94,0.15);
        }

        .btn-success {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-weight: 600;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(34,197,94,0.25);
        }

        .link {
            font-size: 13px;
            color: #16a34a;
            text-decoration: none;
            font-weight: 600;
        }

        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="auth-card">

    <!-- LEFT -->
    <div class="left">
        <div class="brand">Inkcredible</div>
        <div class="subtitle">Create your account</div>
    </div>

    <!-- RIGHT -->
    <div class="right">

        <div class="title">Create account</div>
        <div class="desc">Register to start your loan journey</div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <input type="text" name="name" class="form-control" placeholder="Full Name" required>

            <input type="email" name="email" class="form-control" placeholder="Email Address" required>

            <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" required>

            <input type="number" name="age" class="form-control" placeholder="Age" required>

            <textarea name="address" class="form-control" placeholder="Address" required></textarea>

            <input type="password" name="password" class="form-control" placeholder="Password" required>

            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>

            <button class="btn btn-success w-100">Create Account</button>

            <div class="text-center mt-3">
                <a href="/login" class="link">Already have account? Login</a>
            </div>

        </form>

    </div>

</div>

</body>
</html>