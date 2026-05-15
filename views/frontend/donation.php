<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donation - Team Praise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="/" class="btn btn-outline-secondary mb-4">&larr; Back</a>
        <h1 class="mb-4">Partner With Us</h1>
        <?php if ($flash = flash()): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4">
                    <h5>Bank Transfer</h5>
                    <p class="mb-1"><strong>Bank:</strong> <?= e($bank['bank_name'] ?? 'First Bank of Nigeria') ?></p>
                    <p class="mb-1"><strong>Account Name:</strong> <?= e($bank['account_name'] ?? 'Team Praise Ministry') ?></p>
                    <p class="mb-0"><strong>Account Number:</strong> <?= e($bank['account_number'] ?? '0123456789') ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <form method="POST" action="/donation" class="card border-0 shadow-sm p-4">
                    <?= \App\Core\Csrf::field() ?>
                    <h5>Online Giving</h5>
                    <div class="mb-3"><label class="form-label">Your Name</label><input type="text" name="donor_name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="donor_email" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Amount (NGN)</label><input type="number" name="amount" class="form-control" min="100" required></div>
                    <div class="mb-3"><label class="form-label">Purpose</label><input type="text" name="purpose" class="form-control" value="General Giving"></div>
                    <button type="submit" class="btn btn-warning">Record Gift</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
