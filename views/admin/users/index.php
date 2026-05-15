<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="/admin/dashboard" class="btn btn-outline-secondary mb-4">&larr; Dashboard</a>
        <h1 class="mb-4">Users & Admins</h1>
        <div class="card border-0 shadow-sm mb-3"><div class="card-body">
            <h5>Public Users</h5>
            <p>Found <?= count($users ?? []) ?> users.</p>
        </div></div>
        <div class="card border-0 shadow-sm"><div class="card-body">
            <h5>Administrators</h5>
            <p>Found <?= count($admins ?? []) ?> admins.</p>
        </div></div>
    </div>
</body>
</html>
