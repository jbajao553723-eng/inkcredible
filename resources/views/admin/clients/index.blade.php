<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clients - Inkcredible</title>

    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Space Grotesk', sans-serif;
        }

        body {
            background: #f4f6fb;
        }

        .container-box {
            padding: 25px;
            max-width: 1100px;
            margin: auto;
        }

        .card-box {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .btn-back {
            background: #2563eb;
            color: white;
            padding: 8px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
        }

        table th {
            font-size: 13px;
            color: #6b7280;
        }

        table td {
            font-size: 14px;
        }

        .badge {
            background: #22c55e;
            color: white;
            padding: 5px 10px;
            border-radius: 10px;
        }

        .muted {
            color: #9ca3af;
        }
    </style>
</head>

<body>

<div class="container-box">

    <div class="card-box">

        <div class="header-row">
            <h4 class="mb-0">Registered Clients</h4>
            <a href="/admin/dashboard" class="btn-back">← Back</a>
        </div>

        <table class="table table-hover align-middle">

            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Age</th>
                    <th>Address</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                @forelse($clients as $client)
                <tr>

                    <td>{{ $client->name }}</td>

                    <td>{{ $client->email }}</td>

                    <td>
                        {{ !empty($client->contact_number) ? $client->contact_number : 'Not provided' }}
                    </td>

                    <td>
                        {{ !empty($client->age) ? $client->age : 'Not provided' }}
                    </td>

                    <td>
                        {{ !empty($client->address) ? $client->address : 'Not provided' }}
                    </td>

                    <td>
                        <span class="badge">Active</span>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center muted">
                        No clients found
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

</body>
</html>