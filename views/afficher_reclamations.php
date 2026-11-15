<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754"> 
    <meta charset="UTF-8">
    <title>Mes Réclamations - ONAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .table-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6; }
        .map-link { font-size: 12px; color: #0d6efd; text-decoration: none; display: inline-block; margin-top: 3px; }
        .map-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php?action=dashboard_citoyen"><i class="fa-solid fa-water me-2"></i>ONAS - Espace Citoyen</a>
            <div class="d-flex align-items-center">
                <a href="index.php?action=dashboard_citoyen" class="btn btn-outline-light btn-sm me-2"><i class="fa-solid fa-arrow-left me-1"></i> Retour</a>
                <a href="index.php?action=logout" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i> Déconnexion</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container py-3">
        <div class="card shadow-sm border-0 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-secondary m-0"><i class="fa-solid fa-list-check me-2"></i>Liste de mes réclamations</h3>
                <a href="index.php?action=ajouter" class="btn btn-success btn-sm fw-bold"><i class="fa-solid fa-plus me-1"></i> Nouvelle Réclamation</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Type de Problème</th>
                            <th>Point de Repère & GPS</th>
                            <th>Priorité</th>
                            <th>Photo</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($reclamations) && is_array($reclamations)): ?>
                            <?php foreach ($reclamations as $recla): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($recla['id_reclamation']) ?></td>
                                    <td><strong><?= htmlspecialchars($recla['type_probleme'] ?? '') ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($recla['repere'] ?? '') ?>
                                        <?php if (!empty($recla['latitude']) && !empty($recla['longitude'])): ?>
                                            <br>
                                            <a href="https://www.google.com/maps?q=<?= $recla['latitude'] ?>,<?= $recla['longitude'] ?>" target="_blank" class="map-link">
                                                <i class="fa-solid fa-location-dot me-1"></i> Voir sur la carte
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $priority = $recla['priorite'] ?? 'Normale'; ?>
                                        <span class="badge <?= ($priority === 'Urgente') ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                            <?= htmlspecialchars($priority) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($recla['photo'])): ?>
                                            <a href="<?= htmlspecialchars($recla['photo']) ?>" target="_blank">
                                                <img src="<?= htmlspecialchars($recla['photo']) ?>" alt="Photo réclamation" class="table-img">
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">Aucune</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $statut = $recla['statut'] ?? 'En attente';
                                            $badgeClass = 'bg-secondary';
                                            if ($statut == 'En attente') $badgeClass = 'bg-warning text-dark';
                                            elseif ($statut == 'En cours') $badgeClass = 'bg-primary';
                                            elseif ($statut == 'Terminée' || $statut == 'Traité') $badgeClass = 'bg-success';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statut) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Vous n'avez aucune réclamation pour le moment.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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