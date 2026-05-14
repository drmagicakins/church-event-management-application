<?php
/**
 * Admin Dashboard View
 * Displays key metrics, charts, and recent activity.
 */
?>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
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
        <div class="card border-0 shadow-sm">
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
        <div class="card border-0 shadow-sm">
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
        <div class="card border-0 shadow-sm">
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
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Monthly Donations</h5>
                <canvas id="donationsChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Content Mix</h5>
                <canvas id="contentChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
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
