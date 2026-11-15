<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754">
    <meta charset="UTF-8">
    <title>Dashboard Admin - ONAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; z-index: 100; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 12px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; border-radius: 5px; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card-stat { border: none; border-radius: 10px; }
        .table-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar p-3 d-flex flex-column justify-content-between">
        <div>
            <h3 class="text-center text-white mb-4 py-2 border-bottom">ONAS Admin</h3>
            <ul class="nav flex-column gap-2">
                <li><a href="index.php?action=dashboard_admin" class="active"><i class="fa-solid fa-chart-line me-2"></i> Tableau de bord</a></li>
                <li><a href="index.php?action=liste_citoyens"><i class="fa-solid fa-users me-2"></i> Gestion Citoyens</a></li>
                <li><a href="index.php?action=gestion_equipe"><i class="fa-solid fa-user-shield me-2"></i> Gestion Équipes</a></li>
            </ul>
        </div>
        <div class="border-top pt-3">
            <span class="d-block text-muted small mb-2">Connecté: <?= htmlspecialchars($_SESSION['nom'] ?? 'Admin') ?></span>
            <a href="index.php?action=logout" class="btn btn-outline-danger btn-sm w-100"><i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php 
            $equipes = $equipes ?? []; 
            $filter = $_GET['filter'] ?? 'tous';
            $reclamations = $reclamations ?? [];
        ?>
        
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card card-stat bg-primary text-white p-3">
                    <h6 class="text-uppercase small">Total Réclamations</h6>
                    <h2 class="fw-bold m-0"><?= count($reclamations) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stat bg-warning text-dark p-3">
                    <h6 class="text-uppercase small">En Attente</h6>
                    <h2 class="fw-bold m-0"><?= count(array_filter($reclamations, fn($r) => $r['statut'] == 'En attente')) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stat bg-success text-white p-3">
                    <h6 class="text-uppercase small">Terminées</h6>
                    <h2 class="fw-bold m-0"><?= count(array_filter($reclamations, fn($r) => $r['statut'] == 'Terminée' || $r['statut'] == 'Traité')) ?></h2>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="card shadow-sm border-0 p-4">
            <div class="row align-items-center mb-3">
                <div class="col-md-4"><h4 class="text-secondary m-0">Suivi des Réclamations</h4></div>
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Rechercher..." onkeyup="filterTable()">
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group btn-group-sm">
                        <a href="index.php?action=dashboard_admin&filter=tous" class="btn btn-outline-secondary <?= $filter == 'tous' ? 'active' : '' ?>">Tous</a>
                        <a href="index.php?action=dashboard_admin&filter=attente" class="btn btn-outline-warning <?= $filter == 'attente' ? 'active' : '' ?>">En attente</a>
                        <a href="index.php?action=dashboard_admin&filter=cours" class="btn btn-outline-primary <?= $filter == 'cours' ? 'active' : '' ?>">En cours</a>
                        <a href="index.php?action=dashboard_admin&filter=terminee" class="btn btn-outline-success <?= $filter == 'terminee' ? 'active' : '' ?>">Terminées</a>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="reclamationsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Citoyen</th>
                            <th>Problème</th>
                            <th>Repère</th>
                            <th>Priorité</th>
                            <th>Photo</th>
                            <th>Statut</th>
                            <th>Affectation</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $filtered = array_filter($reclamations, function($r) use ($filter) {
                                if ($filter == 'attente' && $r['statut'] != 'En attente') return false;
                                if ($filter == 'cours' && $r['statut'] != 'En cours') return false;
                                if ($filter == 'terminee' && !in_array($r['statut'], ['Terminée', 'Traité'])) return false;
                                return true;
                            });
                        ?>
                        <?php if (!empty($filtered)): ?>
                            <?php foreach ($filtered as $recla): ?>
                                <?php $isTerminated = in_array($recla['statut'], ['Terminée', 'Traité']); ?>
                                <tr>
                                    <td>#<?= $recla['id_reclamation'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($recla['nom_citoyen'] ?? 'Inconnu') ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($recla['cin'] ?? '') ?></small>
                                    </td>
                                    <td><strong><?= htmlspecialchars($recla['type_probleme']) ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($recla['repere'] ?? '') ?>
                                        <?php if (!empty($recla['latitude'])): ?>
                                            <br><a href="https://www.google.com/maps?q=<?= $recla['latitude'] ?>,<?= $recla['longitude'] ?>" target="_blank" class="small text-primary">📍 GPS</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= ($recla['priorite'] ?? '') === 'Urgente' ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                            <?= htmlspecialchars($recla['priorite'] ?? 'Normale') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($recla['photo'])): ?>
                                            <a href="<?= htmlspecialchars($recla['photo']) ?>" target="_blank"><img src="<?= htmlspecialchars($recla['photo']) ?>" class="table-img"></a>
                                        <?php else: ?>
                                            <span class="text-muted small">Aucune</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($recla['statut']) ?></span></td>
                                    <td>
                                        <form action="index.php?action=affecter" method="POST" class="d-flex gap-1 align-items-center">
                                            <input type="hidden" name="id_reclamation" value="<?= $recla['id_reclamation'] ?>">
                                            <select name="id_equipe" class="form-select form-select-sm" <?= $isTerminated ? 'disabled' : 'required' ?>>
                                                <option value="" disabled <?= empty($recla['affecte_a']) ? 'selected' : '' ?>>Équipe...</option>
                                                <?php foreach ($equipes as $eq): ?>
                                                    <?php $eqId = $eq['id'] ?? $eq['id_employer']; ?>
                                                    <option value="<?= $eqId ?>" <?= (isset($recla['affecte_a']) && $recla['affecte_a'] == $eqId) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($eq['nom']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (!$isTerminated): ?>
                                                <button type="submit" class="btn btn-success btn-sm">OK</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modal-<?= $recla['id_reclamation'] ?>"><i class="fa-solid fa-eye"></i></button>
                                        <a href="index.php?action=supprimer&id=<?= $recla['id_reclamation'] ?>" onclick="return confirm('Confirmer ?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>

                                <!-- Modal -->
                                <div class="modal fade" id="modal-<?= $recla['id_reclamation'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title">Détails #<?= $recla['id_reclamation'] ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h6>Rapport :</h6>
                                                <p class="text-muted"><?= !empty($recla['rapport']) ? nl2br(htmlspecialchars($recla['rapport'])) : "Aucun rapport." ?></p>
                                                <h6 class="mt-3">Photo d'intervention :</h6>
                                                <div class="text-center">
                                                    <?php if (!empty($recla['photo_intervention'])): ?>
                                                        <img src="<?= htmlspecialchars($recla['photo_intervention']) ?>" class="img-fluid rounded" style="max-height: 200px;">
                                                    <?php else: ?>
                                                        <span class="text-muted">Aucune photo</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9" class="text-center text-muted py-4">Aucune réclamation.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterTable() {
            let filter = document.getElementById("searchInput").value.toLowerCase();
            let tr = document.getElementById("reclamationsTable").getElementsByTagName("tr");
            for (let i = 1; i < tr.length; i++) {
                tr[i].style.display = (tr[i].textContent || tr[i].innerText).toLowerCase().includes(filter) ? "" : "none";
            }
        }
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js');
        }
    </script>
</body>
</html>