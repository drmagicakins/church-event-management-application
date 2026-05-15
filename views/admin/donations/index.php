<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donations - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="/admin/dashboard" class="btn btn-outline-secondary mb-4">&larr; Dashboard</a>
        <h1 class="mb-4">Donations Management</h1>
        <div class="card border-0 shadow-sm"><div class="card-body">
            <p>Total Paid: <strong><?= format_money($totalPaid ?? 0) ?></strong></p>
            <p>Found <?= count($donations ?? []) ?> donation records.</p>
        </div></div>
    </div>
</body>
</html>
