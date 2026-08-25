<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inkcredible Lending System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

    <div class="min-h-screen flex flex-col items-center justify-center">

        <!-- HEADER -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold">Inkcredible Lending System</h1>
            <p class="text-gray-600">Simple Loan Management System</p>
        </div>

        <!-- CONTENT -->
        <div class="bg-white p-6 rounded shadow w-96">
            {{ $slot }}
        </div>

    </div>

</body>
</html>