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
<div class="sidebar p-3 d-flex flex-column justify-content-between">
    <div><h3 class="text-center text-white mb-4 py-2 border-bottom">ONAS Admin</h3><ul class="nav flex-column gap-2"><li><a href="index.php?action=dashboard_admin" class="active"><i class="fa-solid fa-chart-line me-2"></i> Tableau de bord</a></li><li><a href="index.php?action=liste_citoyens"><i class="fa-solid fa-users me-2"></i> Gestion Citoyens</a></li><li><a href="index.php?action=gestion_equipe"><i class="fa-solid fa-user-shield me-2"></i> Gestion Équipes</a></li></ul></div>
    <div class="border-top pt-3"><span class="d-block text-muted small mb-2">Connecté: <?= htmlspecialchars($_SESSION['nom'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></span><a href="index.php?action=logout" class="btn btn-outline-danger btn-sm w-100"><i class="fa-solid fa-right-from-bracket me-2"></i> Déconnexion</a></div>
</div>
<div class="main-content">
<?php $equipes = $equipes ?? []; $filter = $_GET['filter'] ?? 'tous'; $reclamations = $reclamations ?? []; ?>
<div class="row mb-4"><div class="col-md-4"><div class="card card-stat bg-primary text-white p-3"><h6 class="text-uppercase small">Total Réclamations</h6><h2 class="fw-bold m-0"><?= count($reclamations) ?></h2></div></div><div class="col-md-4"><div class="card card-stat bg-warning text-dark p-3"><h6 class="text-uppercase small">En Attente</h6><h2 class="fw-bold m-0"><?= count(array_filter($reclamations, fn($r) => ($r['statut'] ?? '') === 'En attente')) ?></h2></div></div><div class="col-md-4"><div class="card card-stat bg-success text-white p-3"><h6 class="text-uppercase small">Terminées</h6><h2 class="fw-bold m-0"><?= count(array_filter($reclamations, fn($r) => in_array($r['statut'] ?? '', ['Terminée', 'Traité'], true))) ?></h2></div></div></div>
<div class="card shadow-sm border-0 p-4">
    <div class="row align-items-center mb-3"><div class="col-md-4"><h4 class="text-secondary m-0">Suivi des Réclamations</h4></div><div class="col-md-4"><input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Rechercher..." onkeyup="filterTable()"></div><div class="col-md-4 text-end"><div class="btn-group btn-group-sm"><a href="index.php?action=dashboard_admin&filter=tous" class="btn btn-outline-secondary <?= $filter === 'tous' ? 'active' : '' ?>">Tous</a><a href="index.php?action=dashboard_admin&filter=attente" class="btn btn-outline-warning <?= $filter === 'attente' ? 'active' : '' ?>">En attente</a><a href="index.php?action=dashboard_admin&filter=cours" class="btn btn-outline-primary <?= $filter === 'cours' ? 'active' : '' ?>">En cours</a><a href="index.php?action=dashboard_admin&filter=terminee" class="btn btn-outline-success <?= $filter === 'terminee' ? 'active' : '' ?>">Terminées</a></div></div></div>
    <div class="table-responsive"><table class="table table-hover align-middle" id="reclamationsTable"><thead class="table-dark"><tr><th>ID</th><th>Citoyen</th><th>Problème</th><th>Repère</th><th>Priorité</th><th>Photo</th><th>Statut</th><th>Affectation</th><th>Actions</th></tr></thead><tbody>
<?php $filtered = array_filter($reclamations, function($r) use ($filter) { if ($filter === 'attente' && ($r['statut'] ?? '') !== 'En attente') return false; if ($filter === 'cours' && ($r['statut'] ?? '') !== 'En cours') return false; if ($filter === 'terminee' && !in_array($r['statut'] ?? '', ['Terminée', 'Traité'], true)) return false; return true; }); ?>
<?php if (!empty($filtered)): foreach ($filtered as $recla): $id = (int) ($recla['id_reclamation'] ?? 0); $isTerminated = in_array($recla['statut'] ?? '', ['Terminée', 'Traité'], true); ?>
<tr><td>#<?= $id ?></td><td><strong><?= htmlspecialchars($recla['nom_citoyen'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8') ?></strong><br><small class="text-muted"><?= htmlspecialchars($recla['cin'] ?? '', ENT_QUOTES, 'UTF-8') ?></small></td><td><strong><?= htmlspecialchars($recla['type_probleme'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></td><td><?= htmlspecialchars($recla['repere'] ?? '', ENT_QUOTES, 'UTF-8') ?><?php if (!empty($recla['latitude']) && !empty($recla['longitude'])): ?><br><a href="https://www.google.com/maps?q=<?= rawurlencode((string) $recla['latitude']) ?>,<?= rawurlencode((string) $recla['longitude']) ?>" target="_blank" rel="noopener noreferrer" class="small text-primary">📍 GPS</a><?php endif; ?></td><td><span class="badge <?= ($recla['priorite'] ?? '') === 'Urgente' ? 'bg-danger' : 'bg-warning text-dark' ?>"><?= htmlspecialchars($recla['priorite'] ?? 'Normale', ENT_QUOTES, 'UTF-8') ?></span></td><td><?php if (!empty($recla['photo'])): ?><a href="<?= htmlspecialchars($recla['photo'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><img src="<?= htmlspecialchars($recla['photo'], ENT_QUOTES, 'UTF-8') ?>" class="table-img" alt="Photo"></a><?php else: ?><span class="text-muted small">Aucune</span><?php endif; ?></td><td><span class="badge bg-secondary"><?= htmlspecialchars($recla['statut'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></td><td><form action="index.php?action=affecter" method="POST" class="d-flex gap-1 align-items-center"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id_reclamation" value="<?= $id ?>"><select name="id_equipe" class="form-select form-select-sm" <?= $isTerminated ? 'disabled' : 'required' ?>><option value="" disabled <?= empty($recla['affecte_a']) ? 'selected' : '' ?>>Équipe...</option><?php foreach ($equipes as $eq): $eqId = (int) ($eq['id_employer'] ?? 0); ?><option value="<?= $eqId ?>" <?= (($recla['affecte_a'] ?? '') == $eqId) ? 'selected' : '' ?>><?= htmlspecialchars($eq['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select><?php if (!$isTerminated): ?><button type="submit" class="btn btn-success btn-sm">OK</button><?php endif; ?></form></td><td><a href="index.php?action=supprimer&id=<?= $id ?>" onclick="return confirm('Confirmer ?')" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a></td></tr>
<?php endforeach; else: ?><tr><td colspan="9" class="text-center text-muted py-4">Aucune réclamation.</td></tr><?php endif; ?>
</tbody></table></div>
</div></div>
<script>
function filterTable() { const filter = document.getElementById('searchInput').value.toLowerCase(); const rows = document.getElementById('reclamationsTable').getElementsByTagName('tr'); for (let i = 1; i < rows.length; i++) rows[i].style.display = (rows[i].textContent || '').toLowerCase().includes(filter) ? '' : 'none'; }
</script>
</body>
</html>