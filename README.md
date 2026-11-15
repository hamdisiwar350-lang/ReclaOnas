# ReclaOnas

Application web ONAS de gestion et de suivi des réclamations citoyennes, avec géolocalisation et preuves d'intervention.

## Configuration

Configurer les variables d'environnement avant de démarrer l'application :

```text
DB_HOST=localhost
DB_NAME=gestion_onas
DB_USER=...
DB_PASS=...
```

Le compte SQL utilisé par l'application doit disposer uniquement des droits nécessaires sur la base `gestion_onas`. Les fichiers déposés sont enregistrés dans `uploads/`; ce dossier doit être accessible en écriture par PHP, mais ne doit pas permettre l'exécution de scripts.

Avec Apache, `uploads/.htaccess` bloque l'exécution des scripts. Avec Nginx, ajouter une règle équivalente pour refuser les extensions PHP dans `uploads/`, car `.htaccess` n'est pas interprété.
