<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin Panel') ?> - Team Praise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="p-3">
                <a href="/admin/dashboard" class="d-flex align-items-center text-white text-decoration-none">
                    <i class="fa-solid fa-music fa-2x text-warning me-2"></i>
                    <div>
                        <strong>Team Praise</strong><br>
                        <small class="text-warning">Admin Suite</small>
                    </div>
                </a>
            </div>
            <nav class="nav flex-column px-2">
                <a class="nav-link text-white" href="/admin/dashboard"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
                <a class="nav-link text-white" href="/admin/events"><i class="fa-solid fa-calendar me-2"></i>Events</a>
                <a class="nav-link text-white" href="/admin/sermons"><i class="fa-solid fa-podcast me-2"></i>Sermons</a>
                <a class="nav-link text-white" href="/admin/blog"><i class="fa-solid fa-newspaper me-2"></i>Blog</a>
                <a class="nav-link text-white" href="/admin/gallery"><i class="fa-solid fa-images me-2"></i>Gallery</a>
                <a class="nav-link text-white" href="/admin/donations"><i class="fa-solid fa-hand-holding-dollar me-2"></i>Donations</a>
                <a class="nav-link text-white" href="/admin/contacts"><i class="fa-solid fa-inbox me-2"></i>Contacts</a>
                <a class="nav-link text-white" href="/admin/prayers"><i class="fa-solid fa-hands-praying me-2"></i>Prayer</a>
                <a class="nav-link text-white" href="/admin/subscribers"><i class="fa-solid fa-envelope me-2"></i>Newsletter</a>
                <a class="nav-link text-white" href="/admin/users"><i class="fa-solid fa-users me-2"></i>Users</a>
                <a class="nav-link text-white" href="/admin/settings"><i class="fa-solid fa-sliders me-2"></i>Settings</a>
            </nav>
        </aside>
        <main class="admin-main">
            <header class="admin-topbar bg-white border-bottom p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= e($title ?? 'Dashboard') ?></h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Welcome, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
                        <form method="POST" action="/admin/logout" class="d-inline">
                            <?= \App\Core\Csrf::field() ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</button>
                        </form>
                    </div>
                </div>
            </header>
            <div class="p-4">
                <?php if ($flash = flash()): ?>
                    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
                        <?= e($flash['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?= $content ?? '' ?>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
