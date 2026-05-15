<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Team Praise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .admin-sidebar { background: #06142e; min-height: 100vh; width: 260px; position: fixed; left: 0; top: 0; padding: 1rem; }
        .admin-sidebar a { color: rgba(255,255,255,.75); padding: .6rem .8rem; display: block; border-radius: .5rem; text-decoration: none; margin-bottom: .25rem; }
        .admin-sidebar a:hover, .admin-sidebar a.active { background: rgba(255,255,255,.08); color: #fff; }
        .admin-main { margin-left: 260px; }
        .admin-topbar { background: white; border-bottom: 1px solid #dee2e6; padding: 1rem 1.5rem; }
        .metric-card { border: 0; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <div class="text-white mb-4">
            <i class="fa-solid fa-music fa-2x text-warning me-2"></i>
            <strong>Team Praise</strong>
        </div>
        <nav>
            <a href="/admin/dashboard" class="active"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
            <a href="/admin/events"><i class="fa-solid fa-calendar me-2"></i>Events</a>
            <a href="/admin/sermons"><i class="fa-solid fa-podcast me-2"></i>Sermons</a>
            <a href="/admin/blog"><i class="fa-solid fa-newspaper me-2"></i>Blog</a>
            <a href="/admin/gallery"><i class="fa-solid fa-images me-2"></i>Gallery</a>
            <a href="/admin/donations"><i class="fa-solid fa-hand-holding-dollar me-2"></i>Donations</a>
            <a href="/admin/contacts"><i class="fa-solid fa-inbox me-2"></i>Contacts</a>
            <a href="/admin/prayers"><i class="fa-solid fa-hands-praying me-2"></i>Prayer</a>
            <a href="/admin/subscribers"><i class="fa-solid fa-envelope me-2"></i>Newsletter</a>
            <a href="/admin/users"><i class="fa-solid fa-users me-2"></i>Users</a>
            <a href="/admin/settings"><i class="fa-solid fa-sliders me-2"></i>Settings</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Dashboard</h4>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
                <form method="POST" action="/admin/logout" class="d-inline">
                    <?= \App\Core\Csrf::field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</button>
                </form>
            </div>
        </header>

        <div class="p-4">
            <?php if ($flash = flash()): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4 mb-4">
                <div class="col-md-6 col-xl-3">
                    <div class="card metric-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Total Events</p>
                                    <h3 class="mb-0 fw-bold"><?= number_format($stats['events'] ?? 0) ?></h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="fa-solid fa-calendar fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card metric-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Total Sermons</p>
                                    <h3 class="mb-0 fw-bold"><?= number_format($stats['sermons'] ?? 0) ?></h3>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                    <i class="fa-solid fa-podcast fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card metric-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Total Donations</p>
                                    <h3 class="mb-0 fw-bold"><?= format_money($stats['donations_total'] ?? 0) ?></h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                    <i class="fa-solid fa-hand-holding-dollar fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card metric-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Subscribers</p>
                                    <h3 class="mb-0 fw-bold"><?= number_format($stats['subscribers'] ?? 0) ?></h3>
                                </div>
                                <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                    <i class="fa-solid fa-users fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <div class="card metric-card">
                        <div class="card-body">
                            <h5 class="card-title">Monthly Donations</h5>
                            <canvas id="donationsChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card metric-card">
                        <div class="card-body">
                            <h5 class="card-title">Content Mix</h5>
                            <canvas id="contentChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card metric-card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Recent Activity</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Actor</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>IP Address</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activity as $log): ?>
                                    <tr>
                                        <td><?= e($log['actor_name'] ?? 'System') ?></td>
                                        <td><?= e($log['action']) ?></td>
                                        <td><span class="badge bg-secondary"><?= e($log['module']) ?></span></td>
                                        <td><code><?= e($log['ip_address']) ?></code></td>
                                        <td><?= format_date($log['created_at'], 'M j, g:i A') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly donations chart
        const donationsCtx = document.getElementById('donationsChart').getContext('2d');
        new Chart(donationsCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($donationsChart ?? [], 'month')) ?>,
                datasets: [{
                    label: 'Donations',
                    data: <?= json_encode(array_column($donationsChart ?? [], 'total')) ?>,
                    borderColor: '#f6c84c',
                    backgroundColor: 'rgba(246, 200, 76, 0.1)',
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });

        // Content mix chart
        const contentCtx = document.getElementById('contentChart').getContext('2d');
        new Chart(contentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Events', 'Sermons', 'Blog', 'Gallery'],
                datasets: [{
                    data: [
                        <?= $stats['events'] ?? 0 ?>,
                        <?= $stats['sermons'] ?? 0 ?>,
                        <?= $stats['blog_posts'] ?? 0 ?>,
                        <?= $stats['gallery'] ?? 0 ?>
                    ],
                    backgroundColor: ['#1647e8', '#f6c84c', '#15b8a6', '#ef4444']
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
</body>
</html>
