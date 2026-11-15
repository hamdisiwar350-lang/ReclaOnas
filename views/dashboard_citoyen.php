<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754">
    <meta charset="UTF-8">
    <title>Espace Citoyen - ONAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fa-solid fa-water me-2"></i>ONAS - Espace Citoyen</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Bienvenue, <?= htmlspecialchars($_SESSION['nom_prenom'] ?? 'Citoyen') ?></span>
                <form action="index.php?action=logout" method="POST" class="d-inline"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"><button type="submit" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i> Déconnexion</button></form>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="card shadow-sm border-0 p-5 bg-white">
                    <h2 class="text-success mb-3">Bienvenue dans votre espace citoyen</h2>
                    <p class="text-muted mb-4">Dans votre espace citoyen, vous pouvez suivre l'état de vos réclamations ou déposer une nouvelle réclamation.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="index.php?action=ajouter" class="btn btn-success"><i class="fa-solid fa-plus me-2"></i> Ajouter une Réclamation</a>
                        <a href="index.php?action=afficher" class="btn btn-outline-success"><i class="fa-solid fa-list me-2"></i> Mes Réclamations</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 1. تسجيل الـ Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js')
                .then(() => console.log('Service Worker Registered Success'))
                .catch((err) => console.log('Service Worker Failed', err));
        }

        // 2. طلب إذن الإشعارات من المواطن أول ما يحل الباج
        if ('Notification' in window && 'serviceWorker' in navigator) {
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    console.log('✅ الإشعارات مفعلة جوها باهي');
                }
            });
        }

        // 3. دالة إظهار الإشعار (باش نستعملوها وقت تتصلح شكاية)
        function showNotification(titre, message) {
            if (Notification.permission === 'granted') {
                navigator.serviceWorker.ready.then(function(registration) {
                    registration.showNotification(titre, {
                        body: message,
                        icon: 'assets/icon-192.png', // تأكد من مسار اللوغو كان عندك تصويرة
                        vibrate: [200, 100, 200]
                    });
                });
            }
        }
    </script>
</body>
</html>