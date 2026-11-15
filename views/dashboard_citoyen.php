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
                <span class="text-white me-3">Bienvenue, <?= htmlspecialchars($_SESSION['nom_prenom'] ?? 'Citoyen', ENT_QUOTES, 'UTF-8') ?></span>
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
                        <button type="button" class="btn btn-outline-secondary" id="enable-notifications">Activer les notifications</button>
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

        const notificationStatuses = <?= json_encode(
            array_column($reclamations ?? [], 'statut', 'id_reclamation'),
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        ) ?>;
        const notificationStorageKey = 'onas-notification-statuses-<?= (int) ($_SESSION['id_citoyen'] ?? 0) ?>';

        function showNotification(titre, message) {
            if ('Notification' in window && Notification.permission === 'granted') {
                navigator.serviceWorker.ready.then(function(registration) {
                    registration.showNotification(titre, {
                        body: message,
                        icon: 'assets/logobas.gif',
                        vibrate: [200, 100, 200]
                    });
                });
            }
        }

        document.getElementById('enable-notifications').addEventListener('click', function() {
            if ('Notification' in window) {
                Notification.requestPermission().then(function(permission) {
                    if (permission === 'granted') {
                        this.textContent = 'Notifications activées';
                        this.disabled = true;
                    }
                }.bind(this));
            }
        });

        const previousStatuses = JSON.parse(localStorage.getItem(notificationStorageKey) || '{}');
        Object.entries(notificationStatuses).forEach(function([id, status]) {
            if (previousStatuses[id] && previousStatuses[id] !== status) {
                showNotification('Réclamation mise à jour', 'La réclamation #' + id + ' est maintenant : ' + status);
            }
        });
        localStorage.setItem(notificationStorageKey, JSON.stringify(notificationStatuses));
    </script>
</body>
</html>