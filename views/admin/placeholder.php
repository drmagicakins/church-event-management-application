<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Team Praise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="/admin/dashboard" class="btn btn-outline-secondary mb-4">&larr; Back to Dashboard</a>
        <h1 class="mb-4"><?= e($title ?? 'Admin Section') ?></h1>
        <div class="alert alert-info">This admin module is ready for extension. Add CRUD forms and DataTables as needed.</div>
        <?php if (!empty($items)): ?>
            <div class="card"><div class="card-body">
                <p>Found <?= count($items) ?> records in this section.</p>
            </div></div>
        <?php endif; ?>
    </div>
</body>
</html>
