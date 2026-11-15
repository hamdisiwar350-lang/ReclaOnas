<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754">
    <meta charset="UTF-8">
    <title>Gestion des Chefs d'équipe - ONAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .main-content { padding: 40px; }
        .card-custom { border: none; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .table thead th { background-color: #212529; color: white; border-bottom: none; }
        .btn-supprimer { background-color: #dc3545; color: white; border: none; padding: 5px 12px; border-radius: 4px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-dark">Gestion des Chefs d'équipe</h2>
            <a href="index.php?action=dashboard_admin" class="btn btn-secondary">Retour au Tableau de bord</a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card card-custom p-4 mb-4">
                    <h4 class="mb-4">Ajouter Chef d'équipe</h4>
                    <form action="index.php?action=store_chef_equipe" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="mb-3"><label class="form-label text-muted">Nom</label><input type="text" name="nom" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label text-muted">Prénom</label><input type="text" name="prenom" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label text-muted">Matricule</label><input type="text" name="matricule" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label text-muted">Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label text-muted">Mot de passe</label><input type="password" name="password" class="form-control" minlength="8" required></div>
                        <div class="mb-4"><label class="form-label text-muted">Spécialité</label><input type="text" name="specialite" class="form-control" required></div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Ajouter</button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-custom p-4">
                    <table class="table align-middle">
                        <thead>
                            <tr><th>Matricule</th><th>Nom du Chef</th><th>Email</th><th>Spécialité</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php if (isset($equipes) && is_array($equipes)): ?>
                                <?php foreach ($equipes as $eq): ?>
                                    <?php if (($eq['role'] ?? '') === 'admin') continue; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($eq['matricule'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($eq['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($eq['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($eq['specialite'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <a href="index.php?action=supprimer_chef_equipe&id=<?= (int) $eq['id_employer'] ?>" class="btn-supprimer" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted">Aucune équipe à afficher.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>