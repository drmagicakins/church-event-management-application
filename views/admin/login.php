<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Team Praise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0a1d42 0%, #1647e8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            max-width: 420px;
            margin: auto;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="fa-solid fa-music fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold">Team Praise Admin</h3>
                <p class="text-muted">Sign in to manage your ministry</p>
            </div>

            <?php if ($flash = flash()): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="/admin/login">
                <?= \App\Core\Csrf::field() ?>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control form-control-lg" required autofocus>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="/admin/forgot" class="small">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Sign In
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="/" class="text-muted small"><i class="fa-solid fa-arrow-left me-1"></i>Back to website</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
