<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<style>
  @page { margin: 0; }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; color: #1a1712; font-size: 10.5px; line-height: 1.5; }
  h1, h2, h3, .serif { font-family: 'DejaVu Serif', serif; }
  .page { padding: 44px 54px; page-break-after: always; position: relative; }
  .page:last-child { page-break-after: auto; }

  .cover-band { background: #1a1712; padding: 88px 54px 66px; text-align: center; }
  .cover-band img { width: 86px; height: 86px; }
  .cover-name { font-family: 'DejaVu Serif', serif; font-size: 38px; color: #fcfaf4; margin-top: 16px; }
  .cover-name b { color: #caa552; }
  .cover-base { color: #caa552; font-size: 11px; letter-spacing: 4px; text-transform: uppercase; margin-top: 10px; }
  .cover-body { padding: 58px 54px; text-align: center; }
  .cover-kicker { color: #b08a37; font-size: 12px; letter-spacing: 5px; text-transform: uppercase; }
  .cover-title { font-family: 'DejaVu Serif', serif; font-size: 25px; margin: 16px 0; }
  .cover-rule { width: 60px; height: 3px; background: #b08a37; margin: 22px auto; }
  .cover-meta { color: #6f695c; font-size: 11px; line-height: 1.9; }

  .kicker { color: #b08a37; font-size: 10px; letter-spacing: 3px; text-transform: uppercase; font-weight: bold; }
  .h-sec { font-family: 'DejaVu Serif', serif; font-size: 21px; margin: 6px 0 4px; }
  .h-rule { width: 46px; height: 3px; background: #b08a37; margin: 9px 0 16px; }
  .h-sub { font-family: 'DejaVu Serif', serif; font-size: 14px; color: #1a1712; margin: 16px 0 7px; padding-bottom: 4px; border-bottom: 1px solid #d8d2c4; }
  p { margin-bottom: 9px; text-align: justify; }
  .lead { font-size: 12px; color: #3a352c; }
  .muted { color: #6f695c; }

  ul.list { margin-left: 16px; margin-bottom: 10px; }
  ul.list li { margin-bottom: 5px; }
  ul.list li b { color: #1a1712; }

  table { width: 100%; border-collapse: collapse; }
  .grid td { vertical-align: top; padding: 6px; }
  .card { background: #fcfaf4; border: 1px solid #d8d2c4; padding: 11px 13px; }
  .card h3 { font-size: 11.5px; margin-bottom: 3px; }
  .card p { font-size: 9.5px; margin: 0; color: #4a443a; text-align: left; }

  .box { background: #f7f3ea; border-left: 3px solid #b08a37; padding: 12px 16px; margin: 6px 0 12px; }
  .box h3 { font-size: 12px; margin-bottom: 4px; }
  .box.dark { background: #1a1712; border-color: #caa552; }
  .box.dark, .box.dark p, .box.dark h3 { color: #f7f3ea; }
  .box.dark h3 { color: #caa552; }

  .feat-tb td { border: 1px solid #e0dacb; padding: 8px 10px; vertical-align: top; }
  .feat-tb .mod { font-family: 'DejaVu Serif', serif; font-size: 11.5px; color: #b08a37; width: 32%; }
  .feat-tb .desc { font-size: 9.5px; color: #3a352c; }

  .step { padding: 8px 0; border-bottom: 1px solid #e6e0d2; }
  .step b.num { display: inline-block; width: 18px; height: 18px; background: #b08a37; color: #fff; text-align: center; border-radius: 50%; font-size: 10px; line-height: 18px; margin-right: 7px; }

  .chk { padding: 5px 0 5px 22px; position: relative; font-size: 10px; border-bottom: 1px dotted #e0dacb; }
  .chk:before { content: ""; position: absolute; left: 0; top: 5px; width: 11px; height: 11px; border: 1.5px solid #b08a37; }
  .tag { display: inline-block; background: #efe9db; color: #6f695c; font-size: 8px; padding: 1px 6px; text-transform: uppercase; letter-spacing: .5px; }
</style>
</head>
<body>

{{-- COUVERTURE --}}
<div class="page" style="padding:0;">
  <div class="cover-band">
    @if($logo)<img src="{{ $logo }}" alt="" />@endif
    <div class="cover-name">Tàak<b>Diàkka</b></div>
    <div class="cover-base">La rencontre bénie · l'union sincère</div>
  </div>
  <div class="cover-body">
    <div class="cover-kicker">Guide technique &amp; de lancement</div>
    <div class="cover-title">Fonctionnalités, déploiement en ligne<br/>&amp; stratégie de lancement</div>
    <div class="cover-rule"></div>
    <div class="cover-meta">Document de référence — équipe &amp; partenaires opérationnels<br/>{{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</div>
  </div>
  <div style="background:#f7f3ea;padding:16px 54px;text-align:center;color:#6f695c;font-size:9.5px;">
    De la plateforme prête à l'emploi jusqu'au lancement réussi — tout ce qu'il faut savoir.
  </div>
</div>

{{-- VUE D'ENSEMBLE --}}
<div class="page">
  <div class="kicker">Panorama</div>
  <div class="h-sec">Vue d'ensemble de la plateforme</div>
  <div class="h-rule"></div>
  <p class="lead">TàakDiàkka est une <b>application web responsive (mobile d'abord)</b> complète et fonctionnelle, prête
  pour la mise en ligne. Elle se compose de quatre grands ensembles :</p>

  <table class="grid"><tr>
    <td width="50%"><div class="card" style="margin-bottom:9px;"><h3>1. L'espace public</h3><p>Page d'accueil, communauté visible, histoires de réussite, tarifs, pages légales, inscription / connexion.</p></div>
    <div class="card"><h3>2. L'espace membre</h3><p>Profil, découverte, demandes d'ami, messagerie, communauté, favoris, abonnement, paramètres.</p></div></td>
    <td width="50%"><div class="card" style="margin-bottom:9px;"><h3>3. L'espace administrateur</h3><p>Tableau de bord, utilisateurs, modération, monétisation, modules, paramètres, contenu, journal.</p></div>
    <div class="card"><h3>4. Les services</h3><p>Paiement, e-mails, notifications temps réel, vérification des photos, file de traitement.</p></div></td>
  </tr></table>

  <div class="h-sub">Briques techniques (en bref)</div>
  <ul class="list">
    <li><b>Application</b> : framework PHP moderne (Laravel), interface web adaptative.</li>
    <li><b>Base de données</b> : stocke membres, profils, messages, publications, abonnements, etc.</li>
    <li><b>File d'attente</b> : traite en arrière-plan les tâches lourdes (vérification photo, e-mails).</li>
    <li><b>Temps réel</b> : serveur WebSocket pour la communauté en direct (avec repli automatique).</li>
    <li><b>Paiement</b> : intégration prête (PayDunya / mobile money), configurable depuis l'admin.</li>
    <li><b>E-mails</b> : envois transactionnels (bienvenue, notifications), configurables depuis l'admin.</li>
  </ul>
  <div class="box dark"><h3>Point clé</h3><p>Tout se pilote depuis l'espace administrateur — tarifs, modules, paiement, e-mails, SEO — sans intervention technique après la mise en ligne.</p></div>
</div>

{{-- LE PROFIL EN DÉTAIL --}}
<div class="page">
  <div class="kicker">Au cœur de l'expérience</div>
  <div class="h-sec">Le profil membre en détail</div>
  <div class="h-rule"></div>

  <div class="h-sub">Le parcours d'accueil (3 étapes)</div>
  <div class="step"><b class="num">1</b><b>Vos informations</b> — genre, date de naissance, région (requis), religion, pratique, profession, présentation.</div>
  <div class="step"><b class="num">2</b><b>Votre photo</b> — ajout d'une photo de profil (alimente aussi la galerie), avec possibilité de passer.</div>
  <div class="step"><b class="num">3</b><b>Votre demande</b> — récapitulatif, taux de complétion, activation de la demande en mariage.</div>

  <div class="h-sub">Le profil &amp; la demande</div>
  <ul class="list">
    <li><b>Informations</b> : genre, âge, région, religion, pratique, situation, enfants, éducation, profession, langues, taille, complexion, bio.</li>
    <li><b>Taux de complétion</b> : encourage le membre à enrichir son profil.</li>
    <li><b>Photos</b> : une photo principale + une galerie (jusqu'à 6) — <b>validation automatique du visage</b> (les images sans visage net sont retirées).</li>
    <li><b>Demande en mariage</b> : déduite du profil, avec statuts <span class="tag">active</span> <span class="tag">en pause</span> <span class="tag">en conversation sérieuse</span>.</li>
    <li><b>Confidentialité</b> : « profil discret » (photo masquée), gestion des e-mails, suppression du compte (RGPD).</li>
  </ul>

  <div class="h-sub">Vérification de confiance — 3 niveaux</div>
  <table class="grid"><tr>
    <td width="33%"><div class="card"><h3>Bronze</h3><p>Numéro de téléphone confirmé.</p></div></td>
    <td width="33%"><div class="card"><h3>Argent</h3><p>Pièce d'identité fournie.</p></div></td>
    <td width="33%"><div class="card"><h3>Or</h3><p>Selfie de vérification (après Argent).</p></div></td>
  </tr></table>
  <p class="muted" style="margin-top:8px;">Chaque palier débloque un badge visible qui renforce la confiance et la visibilité du profil.</p>
</div>

{{-- FONCTIONNALITÉS MEMBRE --}}
<div class="page">
  <div class="kicker">Disponible aujourd'hui</div>
  <div class="h-sec">Fonctionnalités — espace membre</div>
  <div class="h-rule"></div>
  <table class="feat-tb">
    <tr><td class="mod">Découverte &amp; compatibilité</td><td class="desc">Suggestions de profils compatibles (genre opposé, valeurs, région, âge), recherche et filtres avancés, score d'affinité. Les profils « boostés » sont mis en avant.</td></tr>
    <tr><td class="mod">Demandes d'ami</td><td class="desc">Envoyer, accepter ou refuser une demande ; devenir « amis » pour échanger. Notifications associées.</td></tr>
    <tr><td class="mod">Messagerie</td><td class="desc">Conversations privées, accusés de lecture, photos des interlocuteurs ; quota gratuit puis premium pour les échanges illimités.</td></tr>
    <tr><td class="mod">Communauté</td><td class="desc">Publications (avec image), réactions, commentaires imbriqués &amp; « j'aime », thèmes (confession, conseil, témoignage, question), mentions &amp; hashtags, mise à jour en temps réel, membres en ligne, signalement.</td></tr>
    <tr><td class="mod">Favoris · Visiteurs · Matchs</td><td class="desc">Enregistrer des profils, voir qui a consulté le sien, retrouver les intérêts réciproques.</td></tr>
    <tr><td class="mod">Notifications &amp; e-mails</td><td class="desc">Notifications in-app (cloche) + e-mails (bienvenue, demande d'ami, message, vérification), avec option de désactivation (opt-out).</td></tr>
    <tr><td class="mod">Mon abonnement</td><td class="desc">Formule en cours, limites de la version gratuite, avantages premium et mise à niveau en un clic.</td></tr>
  </table>
  <div class="box"><h3>Logique « freemium »</h3><p class="muted">L'accès gratuit permet de découvrir ; les fonctions à forte valeur (demandes d'ami, messages illimités, toutes les photos, visiteurs) sont premium. Chaque règle est activable depuis l'admin.</p></div>
</div>

{{-- ESPACE ADMIN --}}
<div class="page">
  <div class="kicker">Pilotage</div>
  <div class="h-sec">Fonctionnalités — espace administrateur</div>
  <div class="h-rule"></div>
  <table class="feat-tb">
    <tr><td class="mod">Tableau de bord</td><td class="desc">Indicateurs clés : membres, en ligne, inscriptions, publications, signalements, <b>revenus &amp; abonnés payants</b>, graphiques inscriptions et revenus.</td></tr>
    <tr><td class="mod">Utilisateurs</td><td class="desc">Recherche &amp; filtres, fiche détaillée, suspendre / bannir / réactiver, forcer la vérification, réinitialiser le mot de passe, supprimer (RGPD).</td></tr>
    <tr><td class="mod">Modération</td><td class="desc">File des signalements (en attente / résolus / rejetés), suppression du contenu, clôture automatique.</td></tr>
    <tr><td class="mod">Monétisation</td><td class="desc">Formules &amp; boosts (prix, avantages), liste des abonnements, paramètres de paiement PayDunya (test / live).</td></tr>
    <tr><td class="mod">Modules &amp; premium</td><td class="desc">Activer / désactiver des modules entiers, définir les règles gratuites/premium, interrupteur maître de monétisation.</td></tr>
    <tr><td class="mod">Paramètres &amp; SEO</td><td class="desc">Logo, identité, réseaux sociaux, méta / mots-clés, Google Analytics &amp; Meta Pixel, e-mails SMTP, mode maintenance, ouverture des inscriptions.</td></tr>
    <tr><td class="mod">Contenu &amp; pages légales</td><td class="desc">Gestion des témoignages, édition des CGU / confidentialité / mentions légales.</td></tr>
    <tr><td class="mod">Journal &amp; rôles</td><td class="desc">Journal d'activité des admins ; rôles Super administrateur et Modérateur.</td></tr>
  </table>
  <div class="box dark"><h3>Sans retour au code</h3><p>Prix, modules, clés de paiement, e-mails, SEO, pages légales, maintenance : tout est modifiable depuis l'interface, idéal après le déploiement.</p></div>
</div>

{{-- DÉPLOIEMENT --}}
<div class="page">
  <div class="kicker">Mise en ligne</div>
  <div class="h-sec">Préparation au déploiement</div>
  <div class="h-rule"></div>
  <p class="muted">Étapes recommandées pour passer en production sereinement.</p>

  <div class="h-sub">1 · Infrastructure</div>
  <ul class="list">
    <li><b>Nom de domaine</b> + hébergement compatible PHP 8.2+.</li>
    <li><b>Base de données de production</b> : MySQL ou PostgreSQL (plutôt que SQLite utilisé en développement).</li>
    <li><b>HTTPS / SSL</b> activé (obligatoire pour le paiement et la confiance).</li>
  </ul>
  <div class="h-sub">2 · Configuration (.env)</div>
  <ul class="list">
    <li><b>Mode production</b> : APP_ENV=production, APP_DEBUG=false, clé d'application générée.</li>
    <li>Paramètres base de données, e-mail (SMTP) et temps réel renseignés.</li>
    <li>Mise en cache : configuration, routes et vues optimisées pour la performance.</li>
  </ul>
  <div class="h-sub">3 · Services permanents</div>
  <ul class="list">
    <li><b>File d'attente</b> : un « worker » tourne en continu (Supervisor) pour la vérification des photos et l'envoi des e-mails.</li>
    <li><b>Temps réel</b> : le serveur WebSocket (Reverb) tourne en continu, derrière un proxy sécurisé (WSS). La communauté fonctionne même sans (repli automatique).</li>
  </ul>
  <div class="h-sub">4 · Paramétrage depuis l'admin</div>
  <ul class="list">
    <li>Renseigner les <b>clés PayDunya</b> et passer en mode « live ».</li>
    <li>Configurer le <b>SMTP</b> et envoyer un e-mail de test.</li>
    <li>Définir logo, SEO, analytics, réseaux sociaux, pages légales.</li>
    <li>Activer la <b>monétisation</b> quand le paiement est prêt.</li>
  </ul>
  <div class="box"><h3>Sécurité &amp; sauvegardes</h3><p class="muted">Changer le mot de passe administrateur par défaut, désactiver le mode debug, planifier des <b>sauvegardes régulières</b> de la base et des photos téléversées.</p></div>
</div>

{{-- MARKETING --}}
<div class="page">
  <div class="kicker">Réussir le démarrage</div>
  <div class="h-sec">Stratégie marketing &amp; lancement</div>
  <div class="h-rule"></div>

  <div class="h-sub">Avant le lancement</div>
  <ul class="list">
    <li><b>Créer l'attente</b> : teasing sur les réseaux, page d'inscription anticipée, liste de premiers membres.</li>
    <li><b>Amorcer la communauté</b> : préparer des profils et des publications de qualité pour ne jamais démarrer « à vide » — c'est décisif.</li>
    <li><b>Préparer le contenu</b> : témoignages, conseils mariage, charte de bienveillance.</li>
  </ul>
  <div class="h-sub">Acquisition</div>
  <table class="grid"><tr>
    <td width="50%"><div class="card" style="margin-bottom:9px;"><h3>Réseaux sociaux</h3><p>TikTok, Instagram, Facebook : contenus courts sur le mariage, conseils, histoires inspirantes.</p></div>
    <div class="card"><h3>Influenceurs &amp; relais</h3><p>Créateurs et figures de confiance de la communauté ; relais associatifs et religieux.</p></div></td>
    <td width="50%"><div class="card" style="margin-bottom:9px;"><h3>Parrainage</h3><p>Inciter les membres à inviter (bouche-à-oreille, le canal n°1 pour ce type de service).</p></div>
    <div class="card"><h3>Diaspora</h3><p>Ciblage géographique (France, Italie, USA…) où la demande est forte et peu servie.</p></div></td>
  </tr></table>
  <div class="h-sub">Conversion &amp; fidélisation</div>
  <ul class="list">
    <li><b>SEO &amp; contenu</b> : la communauté produit du contenu indexable ; pages tarifs et témoignages optimisées.</li>
    <li><b>E-mails</b> : bienvenue, relances de complétion de profil, notifications d'activité.</li>
    <li><b>Mesure</b> : suivre inscriptions, complétion, conversions premium et rétention (Analytics intégrable depuis l'admin).</li>
  </ul>
  <div class="box dark"><h3>Le piège à éviter</h3><p>Ne jamais lancer une plateforme communautaire « vide ». Amorcez la communauté, modérez dès le premier jour, et soyez réactifs sur le support : la confiance se gagne au démarrage.</p></div>
</div>

{{-- CHECKLIST GO-LIVE --}}
<div class="page">
  <div class="kicker">Avant d'ouvrir les portes</div>
  <div class="h-sec">Checklist de lancement</div>
  <div class="h-rule"></div>
  <table class="grid"><tr>
    <td width="50%">
      <div class="h-sub" style="margin-top:0;">Technique</div>
      <div class="chk">Domaine + HTTPS actifs</div>
      <div class="chk">Base de production migrée &amp; initialisée</div>
      <div class="chk">Compte admin créé, mot de passe changé</div>
      <div class="chk">Mode production (debug désactivé)</div>
      <div class="chk">Worker de file d'attente lancé</div>
      <div class="chk">Serveur temps réel lancé (optionnel)</div>
      <div class="chk">Clés PayDunya en mode live + test OK</div>
      <div class="chk">SMTP configuré + e-mail de test reçu</div>
      <div class="chk">Sauvegardes automatiques planifiées</div>
    </td>
    <td width="50%">
      <div class="h-sub" style="margin-top:0;">Contenu &amp; marketing</div>
      <div class="chk">Logo, SEO, réseaux sociaux renseignés</div>
      <div class="chk">Pages légales (CGU, confidentialité) publiées</div>
      <div class="chk">Tarifs &amp; formules vérifiés</div>
      <div class="chk">Communauté amorcée (profils + publications)</div>
      <div class="chk">Comptes réseaux sociaux prêts</div>
      <div class="chk">Plan de contenu des 4 premières semaines</div>
      <div class="chk">Influenceurs / relais contactés</div>
      <div class="chk">Programme de parrainage défini</div>
      <div class="chk">Outils de mesure (Analytics) en place</div>
    </td>
  </tr></table>

  <div class="box" style="margin-top:14px;"><h3>Le jour J</h3><p class="muted">Ouvrir les inscriptions depuis l'admin, surveiller les performances et les premiers retours, modérer activement, et communiquer régulièrement pour entretenir la dynamique.</p></div>

  <div style="text-align:center;color:#6f695c;font-size:11px;line-height:1.8;margin-top:24px;">
    <b style="color:#1a1712;font-family:'DejaVu Serif',serif;font-size:15px;">TàakDiàkka</b><br/>
    La rencontre bénie · l'union sincère<br/>
    <span style="color:#9a948a;font-size:9px;">Document de référence — © {{ now()->year }}</span>
  </div>
</div>

</body>
</html>
