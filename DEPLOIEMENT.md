# 🚀 Guide de déploiement — TàakDiàkka

Guide pratique pour mettre **TàakDiàkka** en ligne (Laravel 12 / PHP 8.2+).
Suivez les sections dans l'ordre. Les commandes sont à lancer **sur le serveur**, à la racine du projet.

---

## 1. Pré-requis serveur

- **PHP 8.2 ou +** avec les extensions :
  `BCMath, Ctype, cURL, DOM, Fileinfo, Filter, Hash, Mbstring, OpenSSL, PCRE, PDO, Session, Tokenizer, XML`
  **+ `GD`** (obligatoire — traitement et détection des images)
  **+ le pilote PDO de votre base** (`pdo_mysql` ou `pdo_sqlite`)
- **Composer**
- **Serveur web** : Nginx ou Apache, avec la racine pointant sur le dossier **`public/`**
- **HTTPS** (certificat SSL — Let's Encrypt par ex.)
- Une **base de données** (voir §3)

---

## 2. Récupération du code & dépendances

```bash
# Cloner / copier le projet, puis :
composer install --no-dev --optimize-autoloader
cp .env.production.example .env      # puis éditez .env (voir §3)
php artisan key:generate             # génère APP_KEY si absent
```

> ⚠️ Ne déployez pas le dossier `dist/` ni les scripts de test (`storage/*.cjs`, `storage/*.php` de debug).

---

## 3. Configuration `.env` (production)

Éditez `.env` (modèle complet dans `.env.production.example`). **À ne pas rater :**

| Variable | Valeur |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | **`false`** (sinon erreurs visibles publiquement) |
| `APP_URL` | `https://votre-domaine.com` |
| `APP_KEY` | généré par `key:generate` |
| `DB_*` | vos identifiants base (MySQL recommandé) |
| `SESSION_SECURE_COOKIE` | `true` (cookies HTTPS) |
| `MAIL_*` | un vrai **SMTP** (voir §6) |

**Base de données** : SQLite fonctionne pour un petit trafic, mais en production **MySQL/PostgreSQL** est recommandé (meilleure concurrence). Pour MySQL :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=taakdiakka
DB_USERNAME=taakdiakka
DB_PASSWORD=********
```

---

## 4. Migrations & optimisation

```bash
php artisan migrate --force          # crée/maj les tables
php artisan db:seed --force          # OPTIONNEL : données de démo (à éviter en prod réelle)

# Caches de production (à refaire à chaque déploiement) :
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

> Astuce : le script `deploy.sh` enchaîne ces commandes.

---

## 5. File d'attente (IMPORTANT)

La **vérification automatique des photos** (rejet des images non humaines) tourne en tâche de fond (`QUEUE_CONNECTION=database`). **Sans worker, elle ne s'exécute pas.**

Lancez un worker permanent via **Supervisor** :

```ini
; /etc/supervisor/conf.d/taakdiakka-worker.conf
[program:taakdiakka-worker]
command=php /chemin/vers/taakdiakka/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/chemin/vers/taakdiakka/storage/logs/worker.log
```
```bash
supervisorctl reread && supervisorctl update && supervisorctl start taakdiakka-worker
```

---

## 6. E-mails

Le projet envoie : e-mail de **bienvenue** et **notifications**. Configurez un SMTP réel dans `.env` :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.votrefournisseur.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@votre-domaine.com"
MAIL_FROM_NAME="TàakDiàkka"
```
> Testez : créez un compte → vous devez recevoir l'e-mail de bienvenue.

---

## 7. Temps réel (OPTIONNEL)

Les nouvelles publications s'affichent en direct via **Reverb** (WebSocket). **C'est optionnel** : sans serveur Reverb, le fil bascule automatiquement sur une actualisation périodique (polling) — tout fonctionne quand même.

Pour l'activer : renseignez les `REVERB_*` dans `.env`, ouvrez le port WebSocket, et lancez `php artisan reverb:start` (via Supervisor également).

---

## 8. Droits & dossiers

```bash
chmod -R ug+rw storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache public/img
```
- **`public/img`** doit être **inscriptible** : les photos téléversées par les membres y sont écrites.
- ⚠️ **Préservez `public/img/` entre deux déploiements** : il contient à la fois les images de marque **et** les photos des membres. Ne l'écrasez pas lors d'une mise à jour.

---

## 9. Serveur web

**Nginx** (extrait) :
```nginx
server {
    listen 443 ssl;
    server_name votre-domaine.com;
    root /chemin/vers/taakdiakka/public;
    index index.php;

    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    gzip on;                       # compression des assets CSS/JS
    gzip_types text/css application/javascript image/svg+xml;
}
# Rediriger http -> https
server { listen 80; server_name votre-domaine.com; return 301 https://$host$request_uri; }
```
Apache : le `.htaccess` fourni dans `public/` gère déjà la réécriture ; activez `mod_rewrite`, `mod_deflate` (compression) et forcez le HTTPS.

---

## 10. ✅ Checklist avant ouverture au public

- [ ] `APP_DEBUG=false` et `APP_ENV=production`
- [ ] `APP_KEY` généré, `.env` **non accessible** publiquement
- [ ] HTTPS actif + redirection http→https + `SESSION_SECURE_COOKIE=true`
- [ ] Base migrée (`migrate --force`)
- [ ] Caches générés (`config/route/view/event:cache`)
- [ ] **Worker de file lancé** (Supervisor) — vérif photo OK
- [ ] **SMTP configuré** — e-mail de bienvenue reçu
- [ ] `public/img` inscriptible et **sauvegardé**
- [ ] Compression gzip/brotli activée
- [ ] Compte **admin** créé et mot de passe changé
- [ ] (SEO) `APP_URL` correct → `sitemap.xml` et balises Open Graph utilisent le vrai domaine
- [ ] Image de partage **1200×630** déposée (Admin → Réglages SEO) — optionnel
- [ ] Sauvegardes automatiques de la base + de `public/img`

---

## 11. Après chaque mise à jour

```bash
git pull            # ou copie des fichiers (hors public/img et .env)
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache
supervisorctl restart taakdiakka-worker
```

Bonne mise en ligne ! 🤲
