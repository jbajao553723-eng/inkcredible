<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inkcredible - Login</title>

    <!-- MODERN BOLD FONT -->
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
            max-width: 920px;
            display: flex;
            border-radius: 24px;
            overflow: hidden;
            background: white;
            box-shadow: 0 25px 70px rgba(0,0,0,0.10);
        }

        /* LEFT PANEL */
        .left {
            flex: 1;
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
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

        /* RIGHT PANEL */
        .right {
            flex: 1;
            padding: 60px 55px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .desc {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 28px;
        }

        .form-control {
            border-radius: 12px;
            padding: 13px;
            margin-bottom: 16px;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99,102,241,0.12);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-weight: 600;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(99,102,241,0.25);
        }

        .link {
            font-size: 13px;
            color: #4f46e5;
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
        <div class="subtitle">Lending Management System</div>
    </div>

    <!-- RIGHT -->
    <div class="right">

        <div class="title">Welcome back</div>
        <div class="desc">Sign in to continue</div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input type="email" name="email" class="form-control" placeholder="Email address" required>

            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>

            <div class="form-check mb-3">
                <input type="checkbox" onclick="togglePassword()" class="form-check-input">
                <label class="form-check-label">Show password</label>
            </div>

            <button class="btn btn-primary w-100">Sign In</button>

            <div class="text-center mt-3">
                <a href="/register" class="link">Create account</a>
            </div>

        </form>

    </div>

</div>

<script>
function togglePassword() {
    let p = document.getElementById("password");
    p.type = p.type === "password" ? "text" : "password";
}
</script>

</body>
</html>