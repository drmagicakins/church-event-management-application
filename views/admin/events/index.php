<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="/admin/dashboard" class="btn btn-outline-secondary mb-4">&larr; Dashboard</a>
        <h1 class="mb-4">Events Management</h1>
        <?php if ($flash = flash()): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p>Found <?= count($events ?? []) ?> events in the database.</p>
                <p class="text-muted">Extend this view with a DataTable and CRUD form.</p>
            </div>
        </div>
    </div>
</body>
</html>
