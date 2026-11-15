<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754">
    <meta charset="UTF-8">
    <title>Gestion des Citoyens - ONAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 12px 20px; display: block; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; border-radius: 5px; }
        .main-content { margin-left: 250px; padding: 30px; }
    </style>
</head>
<body>

    <!-- نفس الـ Sidebar للربط السريع -->
    <div class="sidebar p-3 d-flex flex-column justify-content-between">
        <div>
            <h3 class="text-center text-white mb-4 py-2 border-bottom">ONAS Admin</h3>
            <ul class="nav flex-column gap-2">
                <li><a href="index.php?action=dashboard_admin"><i class="fa-solid fa-chart-line me-2"></i> Tableau de bord</a></li>
                <li><a href="index.php?action=liste_citoyens" class="active"><i class="fa-solid fa-users me-2"></i> Gestion Citoyens</a></li>
                <li><a href="index.php?action=gestion_equipe"><i class="fa-solid fa-user-shield me-2"></i> Gestion Équipes</a></li>
            </ul>
        </div>
        <div class="border-top pt-3">
            <span class="d-block text-muted small mb-2">Connecté: <?= htmlspecialchars($_SESSION['nom'] ?? 'Admin') ?></span>
            <a href="index.php?action=logout" class="btn btn-outline-danger btn-sm w-100"><i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion</a>
        </div>
    </div>

    <!-- المحتوى -->
    <div class="main-content">
        <div class="card shadow-sm border-0 p-4">
            <h4 class="mb-3 text-secondary"><i class="fa-solid fa-users me-2"></i> Gestion des Citoyens</h4>
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>CIN</th>
                        <th>Nom & Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Etat</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($citoyens)): ?>
                        <?php foreach ($citoyens as $c): ?>
                            <tr>
                                <td><?= $c['id_citoyen'] ?></td>
                                <td><?= $c['cin'] ?></td>
                                <td><?= $c['nom_prenom'] ?></td>
                                <td><?= $c['email'] ?></td>
                                <td><?= $c['telephone'] ?></td>
                                <td>
                                    <span class="badge <?= (isset($c['etat']) && $c['etat'] == 'bloque') ? 'bg-danger' : 'bg-success' ?>">
                                        <?= isset($c['etat']) ? $c['etat'] : 'actif' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if (isset($c['etat']) && $c['etat'] == 'bloque'): ?>
                                        <a href="index.php?action=debloquer&id=<?= $c['id_citoyen'] ?>" class="btn btn-outline-success btn-sm">
                                            <i class="fa-solid fa-lock-open me-1"></i> Débloquer
                                    </a>
                                    <?php else: ?>
                                        <a href="index.php?action=bloquer&id=<?= $c['id_citoyen'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Voulez-vous vraiment bloquer ce citoyen ?')">
                                            <i class="fa-solid fa-lock me-1"></i> Bloquer
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucun citoyen trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       if ('serviceWorker' in navigator) {
          navigator.serviceWorker.register('sw.js')
             .then(() => console.log('Service Worker Registered Success'))
             .catch((err) => console.log('Service Worker Failed', err));
        }
     </script>
</body>
</html>