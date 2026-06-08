# Déploiement — Aperçu statique TàakDiàkka

Le dossier **`dist/`** contient la version **statique** du site (landing + demandes figées),
à partager avec un participant via un **domaine gratuit**, sans serveur.

## 🔄 Régénérer le statique (à chaque étape qu'on veut montrer)

```bash
# 1. Lancer le serveur (s'il ne tourne pas déjà)
php artisan serve

# 2. Dans un autre terminal : exporter
php scripts/export-static.php
```
→ met à jour `dist/`.

## 👀 Prévisualiser `dist/` en local (avant de publier)

```bash
php -S localhost:9000 -t dist
# puis ouvrir http://localhost:9000
```
> Ne pas ouvrir `dist/index.html` en double-clic : les chemins sont absolus (`/img/...`)
> et ne marchent qu'à la racine d'un domaine.

---

## ☁️ Option A — Cloudflare Pages (recommandé, mise à jour auto)

1. Créer un repo GitHub : https://github.com/new → nom `taakdiakka` (privé possible).
2. Pousser le code :
   ```bash
   git branch -M main
   git remote add origin https://github.com/VOTRE_USER/taakdiakka.git
   git push -u origin main
   ```
3. https://dash.cloudflare.com → **Workers & Pages** → **Create** → **Pages** → **Connect to Git**
   - Sélectionner le repo `taakdiakka`
   - **Framework preset** : `None`
   - **Build command** : *(laisser vide)*
   - **Build output directory** : `dist`
   - **Save and Deploy**
4. URL publique : **`https://taakdiakka.pages.dev`** → à envoyer au participant.
5. **Mise à jour** : `php scripts/export-static.php` puis
   ```bash
   git add -A && git commit -m "maj aperçu" && git push
   ```
   → Cloudflare redéploie tout seul.

## ☁️ Option B — Netlify (glisser-déposer, sans Git)

1. `php scripts/export-static.php` (génère `dist/`)
2. Aller sur https://app.netlify.com/drop et **glisser le dossier `dist`**.
3. URL instantanée `https://NOM-ALEATOIRE.netlify.app` (renommable dans *Site settings*).

> Pour Netlify via Git : publish directory = `dist`, build command = vide (voir `netlify.toml`).
