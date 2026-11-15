<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754">
    <meta charset="UTF-8">
    <title>Espace Équipe Technique - ONAS</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .badge-en-cours { background-color: #ffc107; color: black; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .badge-terminee { background-color: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .table-img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        .map-link { font-size: 12px; color: #007bff; text-decoration: none; display: block; margin-top: 4px; }
        .map-link:hover { text-decoration: underline; }
        .btn-action { background: #28a745; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 12px; }
        .btn-action:hover { background: #218838; }
    </style>
</head>
<body>
<div class="container" style="max-width: 1100px; margin: 30px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    
    <!-- رأس الصفحة مع زر الخروج -->
    <div class="header-wrapper" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>🛠️ Espace Équipe Technique - Les Tâches</h2>
        <div>
            <span style="margin-right: 15px; font-weight: bold; color: #555;">Bonjour, <?= htmlspecialchars($_SESSION['nom'] ?? 'Équipe') ?></span>
            <a href="index.php?action=logout" style="background: #dc3545; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 14px;">Déconnexion</a>
        </div>
    </div>
    
    <table style="width: 100%; border-collapse: collapse;" border="1" cellpadding="10">
        <thead>
            <tr style="background: #f8f9fa;">
                <th>ID</th>
                <th>Type de Problème</th>
                <th>Point de Repère & GPS</th>
                <th>Photo Citoyen</th>
                <th>Statut</th>
                <th>Action / Rapport</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($reclamations) && is_array($reclamations)): ?>
                <?php foreach ($reclamations as $recla): ?>
                    <tr>
                        <td>#<?= $recla['id_reclamation'] ?></td>
                        <td><?= htmlspecialchars($recla['type_probleme'] ?? '') ?></td>
                        <td>
                            <?= htmlspecialchars($recla['repere'] ?? '') ?>
                            <?php if (!empty($recla['latitude']) && !empty($recla['longitude'])): ?>
                                <br>
                                <a href="https://www.google.com/maps?q=<?= $recla['latitude'] ?>,<?= $recla['longitude'] ?>" target="_blank" class="map-link">
                                    📍 Voir sur la carte
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($recla['photo'])): ?>
                                <img src="<?= htmlspecialchars($recla['photo']) ?>" alt="Photo" class="table-img">
                            <?php else: ?>
                                <span style="color: #999; font-size: 12px;">Pas de photo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="<?= ($recla['statut'] === 'Terminée') ? 'badge-terminee' : 'badge-en-cours' ?>">
                                <?= htmlspecialchars($recla['statut']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($recla['statut'] !== 'Terminée'): ?>
                                <!-- نموذج إنهاء المهمة ورفع تقرير التدخل والصورة -->
                                <form action="index.php?action=marquer_terminee" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 6px;">
                                          <input type="hidden" name="id_reclamation" value="<?= $recla['id_reclamation'] ?>">
    
                                         <textarea name="rapport" placeholder="Rapport d'intervention..." required style="padding: 4px; font-size: 11px; width: 150px; height: 40px; resize: none;"></textarea>
    
    <!-- رفع أي نوع ملف + التصوير المباشر (Real-time Camera) -->
                                         <input type="file" name="photo_intervention" style="font-size: 10px; width: 160px;">           
                                         <button type="submit" class="btn-action">Marquer Terminée</button>
                                </form>
                            <?php else: ?>
                                <span style="color: #28a745; font-size: 12px; font-weight: bold;">✔ Traité</span>
                             <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="no-data" style="text-align: center; color: #777; padding: 20px;">Aucune tâche assignée pour le moment.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
       if ('serviceWorker' in navigator) {
          navigator.serviceWorker.register('sw.js')
             .then(() => console.log('Service Worker Registered Success'))
             .catch((err) => console.log('Service Worker Failed', err));
        }
     </script>
</body>
</html>