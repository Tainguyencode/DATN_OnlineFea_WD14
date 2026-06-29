<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Users Export</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: rgb(15, 23, 42); margin: 32px; }
        h1 { margin-bottom: 4px; }
        p { color: rgb(100, 116, 139); margin-top: 0; }
        table { border-collapse: collapse; width: 100%; font-size: 12px; }
        th, td { border: 1px solid rgb(226, 232, 240); padding: 8px; text-align: left; }
        th { background: rgb(248, 250, 252); }
        .badge { border-radius: 999px; padding: 3px 8px; font-weight: 700; }
    </style>
</head>
<body>
    <h1>Website học online FEA Users Report</h1>
    <p>Generated at {{ now()->format('d/m/Y H:i') }} · {{ $users->count() }} records</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->deleted_at ? 'deleted' : ($user->is_active ? 'active' : 'blocked') }}</td>
                    <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>window.print();</script>
</body>
</html>
