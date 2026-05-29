#!/bin/bash

# Script de configuration pour CI/CD
set -e

echo "🔧 Configuration de l'environnement CI..."

# Copier le fichier .env pour CI
if [ -f ".env.ci" ]; then
    cp .env.ci .env
    echo "✅ Fichier .env.ci copié"
else
    cp .env.example .env
    echo "✅ Fichier .env.example copié"
fi

# Générer la clé d'application
php artisan key:generate --force
echo "✅ Clé d'application générée"

# Créer les répertoires nécessaires
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Définir les permissions
chmod -R 775 storage bootstrap/cache
echo "✅ Permissions définies"

echo "🎉 Configuration CI terminée avec succès !"