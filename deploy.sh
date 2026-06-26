#!/usr/bin/env bash
# Déploiement / mise à jour TàakDiàkka — à lancer à la racine du projet sur le serveur.
set -e

echo "→ Dépendances (sans dev)…"
composer install --no-dev --optimize-autoloader

echo "→ Migrations…"
php artisan migrate --force

echo "→ Caches de production…"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Décommentez si vous utilisez le disque public Laravel :
# php artisan storage:link

echo "✅ Optimisé."
echo "Pensez à : worker de file (supervisorctl restart taakdiakka-worker) + HTTPS + SMTP."
