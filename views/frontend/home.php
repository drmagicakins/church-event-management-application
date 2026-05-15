<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Praise Official - One Sound. One Praise. One Kingdom.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: system-ui, sans-serif; }
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(6,20,46,.9), rgba(22,71,232,.7)), url('https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=1600') center/cover;
            color: white;
            display: flex;
            align-items: center;
        }
        .brand { font-size: 5rem; font-weight: 900; letter-spacing: -0.05em; }
        .btn-gold { background: #f6c84c; color: #06142e; font-weight: 700; }
        .btn-gold:hover { background: #ffe083; color: #06142e; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background:rgba(6,20,46,.8);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/"><i class="fa-solid fa-music text-warning me-2"></i>Team Praise</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/about">About</a>
                <a class="nav-link" href="/events">Events</a>
                <a class="nav-link" href="/sermons">Sermons</a>
                <a class="nav-link" href="/contact">Contact</a>
                <a class="nav-link" href="/admin/login">Admin</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container text-center">
            <h1 class="brand mb-4">Team Praise</h1>
            <p class="lead mb-4" style="font-size:1.5rem;">One Sound. One Praise. One Kingdom.</p>
            <a href="/events" class="btn btn-gold btn-lg px-5 me-3">Upcoming Events</a>
            <a href="/donation" class="btn btn-outline-light btn-lg px-5">Partner With Us</a>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Welcome to Team Praise</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fa-solid fa-calendar-days fa-3x text-primary mb-3"></i>
                            <h5>Upcoming Events</h5>
                            <p class="text-muted">Join us for powerful worship experiences and revival gatherings.</p>
                            <a href="/events" class="btn btn-outline-primary">View Events</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fa-solid fa-podcast fa-3x text-success mb-3"></i>
                            <h5>Sermons & Teachings</h5>
                            <p class="text-muted">Grow spiritually with audio and video messages from our ministers.</p>
                            <a href="/sermons" class="btn btn-outline-success">Listen Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fa-solid fa-hand-holding-dollar fa-3x text-warning mb-3"></i>
                            <h5>Partner With Us</h5>
                            <p class="text-muted">Support the ministry and help us reach more lives for Christ.</p>
                            <a href="/donation" class="btn btn-outline-warning">Give Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Team Praise Official. All rights reserved.</p>
            <div class="mt-3">
                <a href="#" class="text-white me-3"><i class="fa-brands fa-facebook fa-lg"></i></a>
                <a href="#" class="text-white me-3"><i class="fa-brands fa-instagram fa-lg"></i></a>
                <a href="#" class="text-white me-3"><i class="fa-brands fa-youtube fa-lg"></i></a>
                <a href="#" class="text-white"><i class="fa-brands fa-x-twitter fa-lg"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
