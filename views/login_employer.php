<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Professionnel - ONAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
            border: none;
        }
        .btn-onas {
            background-color: #005b96;
            color: white;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-onas:hover {
            background-color: #004573;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card login-card mt-5">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-secondary">Espace Professionnel</h3>
                        <p class="text-muted">Accès réservé à l'Administration et aux Chefs d'équipe</p>
                    </div>

                    <form action="index.php?action=auth_employer" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        
                        <?php if (isset($erreur)): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <?php echo htmlspecialchars($erreur); ?>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="matricule" class="form-label fw-bold">Matricule :</label>
                            <input type="text" name="matricule" id="matricule" class="form-control form-control-lg" placeholder="Ex: ADMIN01" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">Mot de passe :</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Votre mot de passe" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-onas btn-lg">Se connecter</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });
</script>
<script>
       if ('serviceWorker' in navigator) {
          navigator.serviceWorker.register('sw.js')
             .then(() => console.log('Service Worker Registered Success'))
             .catch((err) => console.log('Service Worker Failed', err));
        }
     </script>
</body>
</html>