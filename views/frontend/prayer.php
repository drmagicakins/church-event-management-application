<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prayer - Team Praise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="/" class="btn btn-outline-secondary mb-4">&larr; Back</a>
        <h1 class="mb-4">Prayer Wall</h1>
        <?php if ($flash = flash()): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-md-6">
                <form method="POST" action="/prayer" class="card border-0 shadow-sm p-4">
                    <?= \App\Core\Csrf::field() ?>
                    <h5>Submit Prayer Request</h5>
                    <div class="mb-3"><label class="form-label">Your Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email (optional)</label><input type="email" name="email" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Prayer Request</label><textarea name="request" class="form-control" rows="5" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Visibility</label><select name="visibility" class="form-select"><option value="private">Private</option><option value="public">Public (after moderation)</option></select></div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="col-md-6">
                <h5>Public Prayer Wall</h5>
                <?php if (empty($wall)): ?>
                    <p class="text-muted">No public prayers yet.</p>
                <?php else: ?>
                    <?php foreach ($wall as $p): ?>
                        <div class="card mb-3"><div class="card-body"><strong><?= e($p['name']) ?></strong><p class="mb-0 mt-2"><?= e($p['request']) ?></p></div></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
