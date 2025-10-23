# Ges_Compte - Docker Setup

Ce guide explique comment déployer l'application Ges_Compte en utilisant Docker.

## Prérequis

- Docker
- Docker Compose

## Structure du projet Docker

```
ges-compte/
├── Dockerfile                    # Configuration de l'image Laravel
├── docker-compose.yml           # Orchestration des services
├── docker-entrypoint.sh         # Script de démarrage
├── .dockerignore               # Fichiers à exclure du build
├── .env.example                # Variables d'environnement
└── docker/
    └── mysql/
        └── init.sql            # Script d'initialisation MySQL
```

## Services Docker

### Application Laravel (app)
- **Image**: PHP 8.2 avec Apache
- **Port**: 8000 (externe) → 80 (interne)
- **Fonctionnalités**:
  - Installation automatique des dépendances Composer
  - Compilation des assets Node.js
  - Génération de la documentation Swagger
  - Migrations automatiques de la base de données

### Base de données PostgreSQL (db)
- **Image**: PostgreSQL 15
- **Port**: 5432
- **Base de données**: ges_compte
- **Utilisateur**: laravel / laravel_password

### pgAdmin (pgadmin)
- **Image**: pgAdmin 4
- **Port**: 8080
- **Email**: admin@ges-compte.com
- **Mot de passe**: admin123
- **Accès**: http://localhost:8080

## Démarrage rapide

1. **Cloner le projet**:
   ```bash
   git clone <repository-url>
   cd ges-compte
   ```

2. **Créer le fichier .env**:
   ```bash
   cp .env.example .env
   ```

3. **Générer la clé d'application**:
   ```bash
   # Si vous avez PHP/Composer installé localement
   php artisan key:generate
   ```

4. **Démarrer les services**:
   ```bash
   docker-compose up -d
   ```

5. **Vérifier le démarrage**:
   ```bash
   docker-compose ps
   ```

## Accès aux services

- **Application**: http://localhost:8000
- **Documentation Swagger**: http://localhost:8000/api/documentation
- **phpMyAdmin**: http://localhost:8080

## Commandes Docker utiles

### Démarrer les services
```bash
docker-compose up -d
```

### Arrêter les services
```bash
docker-compose down
```

### Voir les logs
```bash
# Tous les services
docker-compose logs -f

# Service spécifique
docker-compose logs -f app
docker-compose logs -f db
```

### Accéder aux conteneurs
```bash
# Application
docker-compose exec app bash

# Base de données
docker-compose exec db mysql -u laravel -p ges_compte
```

### Exécuter des commandes Artisan
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan l5-swagger:generate
```

### Reconstruire l'image
```bash
docker-compose build --no-cache
```

## Variables d'environnement

Les variables importantes dans `.env`:

```env
# Application
APP_NAME=Ges_Compte
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000

# Base de données
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=ges_compte
DB_USERNAME=laravel
DB_PASSWORD=laravel_password

# Swagger
L5_SWAGGER_GENERATE_ALWAYS=true
```

## Dépannage

### Problème: Port déjà utilisé
```bash
# Changer les ports dans docker-compose.yml
ports:
  - "8001:80"  # au lieu de 8000:80
  - "8081:80"  # au lieu de 8080:80 pour phpMyAdmin
```

### Problème: Erreur de connexion à la base de données
```bash
# Vérifier que le service db est démarré
docker-compose ps

# Voir les logs de la base de données
docker-compose logs db

# Redémarrer les services
docker-compose restart
```

### Problème: Permissions sur les fichiers
```bash
# Corriger les permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
```

## Déploiement en production

Pour le déploiement en production (ex: Render, Railway, etc.):

1. **Utiliser uniquement le Dockerfile** pour l'application
2. **Configurer une base de données externe** (AWS RDS, PlanetScale, etc.)
3. **Utiliser des variables d'environnement** pour la configuration
4. **Activer HTTPS** et configurer les headers de sécurité

### Variables d'environnement pour la production:
```env
APP_ENV=production
APP_DEBUG=false
DB_HOST=votre-host-de-db
DB_USERNAME=votre-username
DB_PASSWORD=votre-password
```

## Structure des volumes

- `db_data`: Stockage persistant des données MySQL
- `./storage`: Montage du dossier storage de Laravel pour les logs et cache

## Sécurité

- **Ne pas commiter** le fichier `.env` dans Git
- **Utiliser des mots de passe forts** pour la base de données
- **Configurer HTTPS** en production
- **Mettre à jour régulièrement** les images Docker