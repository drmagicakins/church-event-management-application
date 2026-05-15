<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact - Team Praise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="/" class="btn btn-outline-secondary mb-4">&larr; Back</a>
        <h1 class="mb-4">Contact Us</h1>
        <?php if ($flash = flash()): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <form method="POST" action="/contact" class="card border-0 shadow-sm p-4">
                    <?= \App\Core\Csrf::field() ?>
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Subject</label><input type="text" name="subject" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Message</label><textarea name="message" class="form-control" rows="5" required></textarea></div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4">
                    <h5>Contact Information</h5>
                    <p><i class="fa-solid fa-envelope me-2"></i>hello@teampraise.org</p>
                    <p><i class="fa-solid fa-phone me-2"></i>+234 800 000 0000</p>
                    <p><i class="fa-solid fa-location-dot me-2"></i>Lagos, Nigeria</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
