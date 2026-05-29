#!/bin/bash

# Chemin ABSOLU RÉEL
API_PATH="/home/cdiu8226/public_html/bogning/edusmart/api"

# On s'assure d'être dans le bon dossier
cd "$API_PATH" || { echo "Erreur: Dossier introuvable" >> "$API_PATH/deploy.log"; exit 1; }

echo "--- Déploiement du $(date) ---" >> deploy.log

# 1. Installation des dépendances
echo "Installation des dépendances..." >> deploy.log
composer install --no-dev --optimize-autoloader --no-interaction --no-progress >> deploy.log 2>&1

# 2. Artisan (Migrations, Cache, etc.)
echo "Lancement des commandes Artisan..." >> deploy.log
php artisan key:generate --force >> deploy.log 2>&1
php artisan migrate:fresh --force >> deploy.log 2>&1
php artisan db:seed --force >> deploy.log 2>&1
php artisan storage:link >> deploy.log 2>&1
php artisan config:cache >> deploy.log 2>&1
php artisan route:cache >> deploy.log 2>&1

echo "--- Déploiement Terminé avec succès ---" >> deploy.log
cat: e: No such file or directory