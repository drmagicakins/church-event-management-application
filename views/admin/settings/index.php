<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="/admin/dashboard" class="btn btn-outline-secondary mb-4">&larr; Dashboard</a>
        <h1 class="mb-4">Site Settings</h1>
        <?php if ($flash = flash()): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <form method="POST" action="/admin/settings" class="card border-0 shadow-sm p-4">
            <?= $csrf ?>
            <div class="mb-3"><label class="form-label">Site Name</label><input type="text" name="site_name" class="form-control" value="<?= e($settings['site_name'] ?? '') ?>"></div>
            <div class="mb-3"><label class="form-label">Tagline</label><input type="text" name="tagline" class="form-control" value="<?= e($settings['tagline'] ?? '') ?>"></div>
            <div class="mb-3"><label class="form-label">Contact Email</label><input type="email" name="contact_email" class="form-control" value="<?= e($settings['contact_email'] ?? '') ?>"></div>
            <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= e($settings['phone'] ?? '') ?>"></div>
            <div class="mb-3"><label class="form-label">Address</label><input type="text" name="address" class="form-control" value="<?= e($settings['address'] ?? '') ?>"></div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</body>
</html>
