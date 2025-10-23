-- Script d'initialisation de la base de données PostgreSQL
-- Ce fichier sera exécuté lors du premier démarrage du conteneur PostgreSQL

-- Créer la base de données si elle n'existe pas (déjà fait par les variables d'environnement)
-- Mais on peut ajouter des configurations supplémentaires ici si nécessaire

-- Exemple de configuration supplémentaire :
-- CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
-- CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- Vous pouvez ajouter ici des configurations spécifiques à PostgreSQL