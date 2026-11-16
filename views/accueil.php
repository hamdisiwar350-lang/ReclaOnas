<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754"> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail ONAS - Accueil</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar-custom {
            background: linear-gradient(90deg, #004a99 0%, #0073e6 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .hero-section {
            padding: 50px 0 30px;
        }
        .card-custom {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            background-color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .card-custom:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 115, 230, 0.15);
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-citoyen {
            background-color: #e6f2ff;
            color: #0073e6;
        }
        .icon-pro {
            background-color: #fff0e6;
            color: #fd7e14;
        }
        .footer {
            margin-top: auto;
            background-color: #ffffff;
            border-top: 1px solid #eaeaea;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <i class="bi bi-droplet-fill fs-3 me-2"></i>
                ONAS | e-Réclamations
            </a>
        </div>
    </nav>

    <div class="container hero-section flex-grow-1">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-dark mb-3">Bienvenue sur le portail d'assistance</h1>
            <p class="text-muted fs-5">Un service rapide et transparent pour la gestion de vos requêtes</p>
        </div>

        <div class="row justify-content-center g-4">
            
            <div class="col-md-5 col-lg-4">
                <div class="card card-custom h-100 p-4 text-center">
                    <div class="card-body d-flex flex-column">
                        <div class="icon-wrapper icon-citoyen">
                            <i class="bi bi-person-heart fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">Espace Citoyen</h4>
                        <p class="text-muted mb-4">Signalez un incident (fuite, refoulement...) et suivez l'avancement de votre dossier en temps réel.</p>
                        <div class="mt-auto">
                            <a href="index.php?action=login_citoyen" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                                Accéder <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 col-lg-4">
                <div class="card card-custom h-100 p-4 text-center">
                    <div class="card-body d-flex flex-column">
                        <div class="icon-wrapper icon-pro">
                            <i class="bi bi-shield-lock-fill fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">Espace Professionnel</h4>
                        <p class="text-muted mb-4">Accès sécurisé pour les administrateurs et les chefs d'équipe de l'ONAS pour gérer les interventions.</p>
                        <div class="mt-auto">
                            <a href="index.php?action=login_employer" class="btn btn-outline-dark btn-lg w-100 rounded-pill fw-bold">
                                S'authentifier <i class="bi bi-box-arrow-in-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="footer py-3 text-center">
        <div class="container">
            <small class="text-muted fw-semibold">
                &copy; 2026 Office National de l'Assainissement (ONAS) - Tous droits réservés.
            </small>
        </div>
    </footer>
    

    <!-- Bootstrap JS -->
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