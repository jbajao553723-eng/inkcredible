<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Files too large | Inkcredible</title>
    <style>
        *{box-sizing:border-box}body{display:grid;min-height:100vh;margin:0;padding:24px;place-items:center;color:#344054;background:#f8fafc;font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif}.card{width:min(100%,520px);padding:34px;background:#fff;border:1px solid #e4e7ec;border-radius:18px;box-shadow:0 18px 50px rgba(15,23,42,.08)}.code{display:inline-flex;padding:6px 10px;color:#4338ca;background:#eef2ff;border-radius:999px;font-size:12px;font-weight:800}.card h1{margin:18px 0 10px;color:#101828;font-size:28px;letter-spacing:-.03em}.card p{margin:0;color:#667085;font-size:14px;line-height:1.65}.card ul{margin:18px 0 24px;padding-left:20px;color:#475467;font-size:13px;line-height:1.7}.button{display:inline-flex;padding:11px 18px;align-items:center;justify-content:center;color:#fff;background:#4f46e5;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none}.button:hover{background:#4338ca}
    </style>
</head>
<body>
<main class="card">
    <span class="code">Upload too large</span>
    <h1>Please choose smaller files</h1>
    <p>The combined verification upload exceeded the server limit. Your information was not submitted.</p>
    <ul>
        <li>Use JPG or PNG images; the app will optimize them automatically.</li>
        <li>Keep PDF files under 1 MB.</li>
        <li>Keep all selected files under 3.6 MB combined.</li>
    </ul>
    <a class="button" href="{{ url()->previous() }}">Return to verification</a>
</main>
</body>
</html>
