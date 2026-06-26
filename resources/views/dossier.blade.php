<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<style>
  @page { margin: 0; }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; color: #1a1712; font-size: 11px; line-height: 1.55; }
  h1, h2, h3, .serif { font-family: 'DejaVu Serif', serif; }
  .page { padding: 44px 54px; page-break-after: always; position: relative; }
  .page:last-child { page-break-after: auto; }

  /* Couverture */
  .cover-band { background: #1a1712; padding: 90px 54px 70px; text-align: center; }
  .cover-band img { width: 90px; height: 90px; }
  .cover-name { font-family: 'DejaVu Serif', serif; font-size: 40px; color: #fcfaf4; margin-top: 18px; letter-spacing: 1px; }
  .cover-name b { color: #caa552; }
  .cover-base { color: #caa552; font-size: 12px; letter-spacing: 4px; text-transform: uppercase; margin-top: 10px; }
  .cover-body { padding: 60px 54px; text-align: center; }
  .cover-kicker { color: #b08a37; font-size: 12px; letter-spacing: 5px; text-transform: uppercase; }
  .cover-title { font-family: 'DejaVu Serif', serif; font-size: 26px; margin: 16px 0; color: #1a1712; }
  .cover-rule { width: 60px; height: 3px; background: #b08a37; margin: 22px auto; }
  .cover-meta { color: #6f695c; font-size: 11px; line-height: 1.9; }

  /* Sections */
  .kicker { color: #b08a37; font-size: 10px; letter-spacing: 3px; text-transform: uppercase; font-weight: bold; }
  .h-sec { font-family: 'DejaVu Serif', serif; font-size: 22px; color: #1a1712; margin: 6px 0 4px; }
  .h-rule { width: 46px; height: 3px; background: #b08a37; margin: 10px 0 20px; }
  p { margin-bottom: 11px; text-align: justify; }
  .lead { font-size: 12.5px; color: #3a352c; }
  .muted { color: #6f695c; }
  strong, b { color: #1a1712; }

  .box { background: #f7f3ea; border-left: 3px solid #b08a37; padding: 14px 18px; margin-bottom: 14px; }
  .box h3 { font-size: 13px; color: #1a1712; margin-bottom: 5px; }

  table { width: 100%; border-collapse: collapse; }
  .grid td { vertical-align: top; padding: 7px; }
  .card { background: #fcfaf4; border: 1px solid #d8d2c4; padding: 13px 15px; }
  .card h3 { font-size: 12.5px; color: #1a1712; margin-bottom: 4px; }
  .card .ic { color: #b08a37; font-weight: bold; }
  .card p { font-size: 10px; margin: 0; color: #4a443a; text-align: left; }

  /* BMC */
  .bmc td { border: 1px solid #d8d2c4; padding: 10px 11px; vertical-align: top; width: 33.33%; }
  .bmc h4 { font-size: 10.5px; color: #b08a37; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; }
  .bmc ul { margin-left: 13px; }
  .bmc li { font-size: 9.5px; color: #3a352c; margin-bottom: 3px; line-height: 1.4; }

  /* Pricing */
  .price-tb td { border: 1px solid #d8d2c4; padding: 0; vertical-align: top; }
  .pcell { padding: 16px 16px 18px; }
  .pcell.feat { background: #1a1712; }
  .pname { font-family: 'DejaVu Serif', serif; font-size: 16px; color: #1a1712; }
  .pcell.feat .pname { color: #fcfaf4; }
  .ptag-r { font-size: 9px; color: #b08a37; text-transform: uppercase; letter-spacing: 1px; }
  .pprice { font-family: 'DejaVu Serif', serif; font-size: 22px; color: #b08a37; margin: 8px 0 2px; }
  .pcell.feat .pprice { color: #caa552; }
  .pold { color: #9a948a; font-size: 11px; text-decoration: line-through; }
  .pcell ul { margin: 10px 0 0 14px; }
  .pcell li { font-size: 9.5px; margin-bottom: 4px; line-height: 1.35; }
  .pcell.feat li, .pcell.feat p { color: #e8e2d4; }
  .recommend { display: inline-block; background: #b08a37; color: #fff; font-size: 8px; letter-spacing: 1px; text-transform: uppercase; padding: 2px 8px; }

  /* Roadmap */
  .phase { border-left: 2px solid #d8d2c4; padding: 0 0 16px 18px; position: relative; }
  .phase-dot { position: absolute; left: -6px; top: 2px; width: 10px; height: 10px; background: #b08a37; border-radius: 50%; }
  .phase h3 { font-size: 13px; color: #1a1712; }
  .phase .when { font-size: 9px; color: #b08a37; text-transform: uppercase; letter-spacing: 1px; }
  .phase p { font-size: 10px; margin: 3px 0 0; }

  .stat-tb td { text-align: center; padding: 14px 8px; background: #f7f3ea; border: 4px solid #fff; }
  .stat-n { font-family: 'DejaVu Serif', serif; font-size: 22px; color: #b08a37; }
  .stat-l { font-size: 9px; color: #6f695c; text-transform: uppercase; letter-spacing: .5px; }

  .cta { background: #1a1712; color: #fcfaf4; padding: 22px 26px; }
  .cta h3 { color: #caa552; font-size: 16px; margin-bottom: 8px; }
  .cta p { color: #e8e2d4; margin-bottom: 8px; }
  .footer-note { color: #9a948a; font-size: 9px; text-align: center; margin-top: 26px; }
</style>
</head>
<body>

{{-- ===================== COUVERTURE ===================== --}}
<div class="page" style="padding:0;">
  <div class="cover-band">
    @if($logo)<img src="{{ $logo }}" alt="" />@endif
    <div class="cover-name">Tàak<b>Diàkka</b></div>
    <div class="cover-base">La rencontre bénie · l'union sincère</div>
  </div>
  <div class="cover-body">
    <div class="cover-kicker">Dossier de présentation</div>
    <div class="cover-title">La maison matrimoniale du Sénégal<br/>et de la diaspora</div>
    <div class="cover-rule"></div>
    <div class="cover-meta">
      Document destiné aux partenaires, adhérents et investisseurs<br/>
      {{ now()->locale('fr')->isoFormat('MMMM YYYY') }} · Confidentiel
    </div>
  </div>
  <div style="background:#f7f3ea;padding:18px 54px;text-align:center;color:#6f695c;font-size:10px;">
    « Favoriser des unions sincères, durables et bénies, dans le respect de nos valeurs. »
  </div>
</div>

{{-- ===================== RÉSUMÉ EXÉCUTIF ===================== --}}
<div class="page">
  <div class="kicker">En bref</div>
  <div class="h-sec">Résumé exécutif</div>
  <div class="h-rule"></div>

  <p class="lead"><b>TàakDiàkka</b> est une maison matrimoniale digitale, premium et bienveillante, pensée pour la
  communauté sénégalaise et sa diaspora. Elle réunit en un seul lieu des <b>profils vérifiés</b>, une <b>mise en
  relation guidée vers le mariage</b> et une <b>communauté d'entraide</b> fidèle à nos valeurs culturelles et religieuses.</p>

  <p>Là où les applications de rencontre classiques privilégient l'éphémère, TàakDiàkka assume une promesse claire :
  des rencontres <b>sérieuses</b>, <b>respectueuses</b> et orientées vers une <b>union durable</b>. La plateforme est
  conçue « mobile d'abord », pour une jeunesse connectée qui vit sur son smartphone.</p>

  <table class="stat-tb" style="margin:22px 0;">
    <tr>
      <td><div class="stat-n">3</div><div class="stat-l">Niveaux de vérification</div></td>
      <td><div class="stat-n">100%</div><div class="stat-l">Orienté mariage</div></td>
      <td><div class="stat-n">2</div><div class="stat-l">Marchés : Sénégal + diaspora</div></td>
      <td><div class="stat-n">4 900</div><div class="stat-l">FCFA / mois (premium)</div></td>
    </tr>
  </table>

  <div class="box">
    <h3>Notre proposition de valeur</h3>
    <p class="muted">Un espace de confiance, vérifié et culturellement ancré, qui transforme l'intention de mariage
    en rencontres concrètes — soutenu par une communauté bienveillante et un modèle économique sain par abonnement.</p>
  </div>

  <div class="box" style="border-color:#1a1712;background:#fcfaf4;">
    <h3>Ce que nous recherchons</h3>
    <p class="muted">Des <b>partenaires</b> et <b>investisseurs</b> partageant notre vision, pour accélérer le
    lancement, la croissance de la communauté et l'expansion vers la diaspora.</p>
  </div>
</div>

{{-- ===================== CONTEXTE & PROBLÈME ===================== --}}
<div class="page">
  <div class="kicker">Le marché</div>
  <div class="h-sec">Contexte &amp; problème</div>
  <div class="h-rule"></div>

  <p class="lead">Trouver un conjoint sérieux, dans le respect de ses valeurs, reste un véritable défi pour les
  jeunes adultes sénégalais et de la diaspora.</p>

  <table class="grid"><tr>
    <td width="50%">
      <div class="card" style="margin-bottom:10px;">
        <h3>Des outils inadaptés</h3>
        <p>Les applications de rencontre internationales ne correspondent ni à la finalité (le mariage), ni au cadre culturel et religieux de notre communauté.</p>
      </div>
      <div class="card">
        <h3>Un manque de confiance</h3>
        <p>Faux profils, intentions floues, absence de vérification : la méfiance freine les rencontres réellement sérieuses.</p>
      </div>
    </td>
    <td width="50%">
      <div class="card" style="margin-bottom:10px;">
        <h3>Une demande forte</h3>
        <p>Une jeunesse nombreuse, connectée et mobile, attachée à ses valeurs, mais sans espace dédié et de confiance pour se rencontrer.</p>
      </div>
      <div class="card">
        <h3>Une diaspora isolée</h3>
        <p>Loin du pays, beaucoup peinent à rencontrer des partenaires partageant la même culture et les mêmes aspirations.</p>
      </div>
    </td>
  </tr></table>

  <div class="box" style="margin-top:16px;">
    <h3>L'opportunité</h3>
    <p class="muted">Un marché mal servi, une attente culturelle claire et un usage massif du smartphone : les
    conditions sont réunies pour une plateforme matrimoniale <b>de référence</b>, pensée par et pour la communauté.</p>
  </div>
</div>

{{-- ===================== SOLUTION & OBJECTIFS ===================== --}}
<div class="page">
  <div class="kicker">Notre réponse</div>
  <div class="h-sec">La solution &amp; notre vision</div>
  <div class="h-rule"></div>

  <p class="lead">TàakDiàkka est une plateforme matrimoniale <b>premium, vérifiée et communautaire</b>, qui facilite
  des unions sincères dans un cadre de confiance et de respect.</p>

  <table class="grid"><tr>
    <td width="33%"><div class="card"><h3><span class="ic">✓</span> Confiance</h3><p>Profils vérifiés en 3 niveaux et modération active : on sait à qui l'on parle.</p></div></td>
    <td width="33%"><div class="card"><h3><span class="ic">✓</span> Pertinence</h3><p>Compatibilité guidée selon les valeurs, la région, la pratique et les attentes.</p></div></td>
    <td width="33%"><div class="card"><h3><span class="ic">✓</span> Communauté</h3><p>Un espace bienveillant d'échange, de conseils et de témoignages inspirants.</p></div></td>
  </tr></table>

  <div class="kicker" style="margin-top:24px;">Nos objectifs</div>
  <div class="h-rule"></div>
  <div class="phase"><div class="phase-dot"></div><div class="when">Court terme</div><h3>Lancer &amp; fédérer</h3><p>Réussir le lancement au Sénégal, constituer une première communauté active et fidèle, et installer la confiance.</p></div>
  <div class="phase"><div class="phase-dot"></div><div class="when">Moyen terme</div><h3>Convertir &amp; monétiser</h3><p>Développer l'abonnement premium, multiplier les mises en relation réussies et les premiers témoignages d'unions.</p></div>
  <div class="phase" style="border-color:#fff;"><div class="phase-dot"></div><div class="when">Long terme</div><h3>Étendre à la diaspora</h3><p>Devenir la référence matrimoniale de la communauté sénégalaise dans le monde.</p></div>
</div>

{{-- ===================== FONCTIONNALITÉS ===================== --}}
<div class="page">
  <div class="kicker">L'expérience</div>
  <div class="h-sec">Les fonctionnalités clés</div>
  <div class="h-rule"></div>
  <p class="muted">Une expérience complète, simple et soignée, accessible comme une véritable application mobile.</p>

  <table class="grid" style="margin-top:8px;">
    <tr>
      <td width="50%"><div class="card" style="margin-bottom:10px;"><h3>Profils vérifiés (Bronze · Argent · Or)</h3><p>Téléphone, pièce d'identité et selfie : un gage de sérieux et de sécurité, valorisé par un badge.</p></div></td>
      <td width="50%"><div class="card" style="margin-bottom:10px;"><h3>Compatibilité guidée</h3><p>Des suggestions pertinentes selon le profil, les valeurs et les critères de chacun.</p></div></td>
    </tr>
    <tr>
      <td><div class="card" style="margin-bottom:10px;"><h3>Demande en mariage</h3><p>Chaque membre exprime clairement son intention : une démarche assumée et respectueuse.</p></div></td>
      <td><div class="card" style="margin-bottom:10px;"><h3>Messagerie encadrée</h3><p>Des échanges privés et bienveillants, avec accusés de lecture et règles de bonne conduite.</p></div></td>
    </tr>
    <tr>
      <td><div class="card" style="margin-bottom:10px;"><h3>Communauté bienveillante</h3><p>Confessions, conseils et témoignages : entraide et inspiration au quotidien.</p></div></td>
      <td><div class="card" style="margin-bottom:10px;"><h3>Sécurité &amp; confidentialité</h3><p>Modération, signalement, profils discrets et contrôle des données personnelles.</p></div></td>
    </tr>
    <tr>
      <td><div class="card"><h3>Photos authentiques</h3><p>Validation des photos pour garantir de vrais visages et des profils crédibles.</p></div></td>
      <td><div class="card"><h3>Mobile d'abord</h3><p>Navigation fluide façon application, optimisée pour smartphones et tablettes.</p></div></td>
    </tr>
  </table>
</div>

{{-- ===================== OFFRES & ABONNEMENTS ===================== --}}
<div class="page">
  <div class="kicker">Modèle de revenus</div>
  <div class="h-sec">Offres &amp; abonnements</div>
  <div class="h-rule"></div>
  <p class="muted">Un modèle <b>freemium</b> : un accès gratuit pour découvrir, et des formules premium qui débloquent
  les fonctionnalités à forte valeur.</p>

  @php
    $display = $plans->count() ? $plans : collect([
      (object)['name'=>'Découverte','price'=>0,'compare_at_price'=>null,'tagline'=>'Pour explorer','is_premium'=>false,'is_free'=>true,'duration_days'=>null,'features'=>['Créer son profil et sa demande','Parcourir les membres','Accès à la communauté']],
      (object)['name'=>'Premium Mensuel','price'=>4900,'compare_at_price'=>6000,'tagline'=>"L'expérience complète",'is_premium'=>true,'is_free'=>false,'duration_days'=>30,'features'=>["Demandes d'ami",'Messages illimités','Toutes les photos','Voir ses visiteurs','Badge Premium']],
      (object)['name'=>'Premium Annuel','price'=>49000,'compare_at_price'=>58800,'tagline'=>'2 mois offerts','is_premium'=>true,'is_free'=>false,'duration_days'=>365,'features'=>['Tous les avantages Premium','Meilleur tarif annuel','Support prioritaire']],
    ]);
  @endphp

  <table class="price-tb" style="margin:16px 0;"><tr>
    @foreach($display as $p)
      @php $feat = $p->is_premium && ($p->duration_days ?? 0) <= 31; @endphp
      <td width="33%">
        <div class="pcell {{ $feat ? 'feat' : '' }}">
          @if($feat)<span class="recommend">Recommandé</span><br/><br/>@endif
          <div class="ptag-r">{{ $p->tagline }}</div>
          <div class="pname">{{ $p->name }}</div>
          <div class="pprice">
            @if(($p->price ?? 0) <= 0) Gratuit @else {{ number_format($p->price, 0, ',', ' ') }} <span style="font-size:10px;">FCFA</span>@endif
          </div>
          @if(!empty($p->compare_at_price))<span class="pold">{{ number_format($p->compare_at_price,0,',',' ') }} FCFA</span>@endif
          <ul>
            @foreach(($p->features ?? []) as $f)<li>{{ $f }}</li>@endforeach
          </ul>
        </div>
      </td>
    @endforeach
  </tr></table>

  <table class="grid"><tr>
    <td width="55%">
      <div class="box"><h3>Boosts de visibilité</h3>
        <p class="muted">Des achats ponctuels pour mettre son profil en avant et multiplier les visites :
          @if($boosts->count()){{ $boosts->pluck('name')->implode(' · ') }}@else mise en avant 24h, 7 jours ou 30 jours @endif.
        </p>
      </div>
    </td>
    <td width="45%">
      <div class="box" style="border-color:#1a1712;background:#fcfaf4;"><h3>Paiement local</h3>
        <p class="muted">Wave, Orange Money, Free Money et carte bancaire, via une intégration de paiement adaptée au marché.</p>
      </div>
    </td>
  </tr></table>
</div>

{{-- ===================== BUSINESS MODEL CANVAS ===================== --}}
<div class="page">
  <div class="kicker">Vue d'ensemble</div>
  <div class="h-sec">Business Model Canvas</div>
  <div class="h-rule"></div>

  <table class="bmc">
    <tr>
      <td><h4>Partenaires clés</h4><ul><li>Prestataires de paiement (Wave, Orange Money, PayDunya)</li><li>Influenceurs &amp; médias communautaires</li><li>Mosquées, associations, organisateurs d'événements</li><li>Relais diaspora</li></ul></td>
      <td><h4>Activités clés</h4><ul><li>Mise en relation &amp; vérification</li><li>Animation de la communauté</li><li>Modération &amp; confiance</li><li>Acquisition &amp; fidélisation</li></ul></td>
      <td><h4>Proposition de valeur</h4><ul><li>Rencontres sérieuses orientées mariage</li><li>Profils vérifiés &amp; cadre de confiance</li><li>Ancrage culturel et religieux</li><li>Communauté bienveillante</li></ul></td>
    </tr>
    <tr>
      <td><h4>Ressources clés</h4><ul><li>La plateforme &amp; sa technologie</li><li>La communauté de membres</li><li>La marque &amp; la confiance</li><li>L'équipe</li></ul></td>
      <td><h4>Relations clients</h4><ul><li>Accompagnement bienveillant</li><li>Notifications &amp; e-mails personnalisés</li><li>Support &amp; modération</li><li>Témoignages &amp; communauté</li></ul></td>
      <td><h4>Canaux</h4><ul><li>Application web mobile</li><li>Réseaux sociaux (TikTok, Instagram…)</li><li>Bouche-à-oreille &amp; parrainage</li><li>Relais diaspora</li></ul></td>
    </tr>
    <tr>
      <td width="50%"><h4>Structure de coûts</h4><ul><li>Hébergement &amp; technique</li><li>Marketing &amp; acquisition</li><li>Modération &amp; support</li><li>Frais de paiement</li></ul></td>
      <td colspan="2" width="50%"><h4>Sources de revenus</h4><ul><li>Abonnements premium (mensuel / annuel)</li><li>Boosts de visibilité</li><li>Mises en avant &amp; services à valeur ajoutée (à venir)</li><li>Partenariats &amp; événements (à venir)</li></ul></td>
    </tr>
  </table>

  <div class="box" style="margin-top:18px;">
    <h3>Un modèle sain et évolutif</h3>
    <p class="muted">Des revenus récurrents par abonnement, complétés par des achats ponctuels (boosts), avec une
    structure de coûts maîtrisée — un modèle qui s'améliore à mesure que la communauté grandit.</p>
  </div>
</div>

{{-- ===================== MARCHÉ & AVANTAGES ===================== --}}
<div class="page">
  <div class="kicker">Positionnement</div>
  <div class="h-sec">Marché cible &amp; avantages</div>
  <div class="h-rule"></div>

  <div class="box"><h3>Notre cible</h3>
    <p class="muted">Jeunes adultes (≈ 20–40 ans) du Sénégal et de la diaspora, attachés à leurs valeurs culturelles
    et religieuses, en recherche d'une union sérieuse — et utilisateurs intensifs du smartphone.</p>
  </div>

  <div class="kicker" style="margin-top:18px;">Nos avantages concurrentiels</div>
  <div class="h-rule"></div>
  <table class="grid"><tr>
    <td width="50%">
      <div class="card" style="margin-bottom:10px;"><h3>Ancrage culturel &amp; religieux</h3><p>Une plateforme pensée pour nos valeurs — un positionnement que les acteurs internationaux ne peuvent occuper.</p></div>
      <div class="card"><h3>La confiance par la vérification</h3><p>Des profils vérifiés et modérés : le cœur de notre différence et de notre réputation.</p></div>
    </td>
    <td width="50%">
      <div class="card" style="margin-bottom:10px;"><h3>L'effet communauté</h3><p>Une communauté active crée de la valeur et de la rétention difficiles à copier.</p></div>
      <div class="card"><h3>Un premium accessible</h3><p>Une tarification adaptée au pouvoir d'achat local, avec paiement mobile.</p></div>
    </td>
  </tr></table>

  <div class="box" style="margin-top:16px;border-color:#1a1712;background:#fcfaf4;">
    <h3>Impact social</h3>
    <p class="muted">Au-delà du business, TàakDiàkka porte une mission : <b>favoriser des unions sincères et durables</b>,
    renforcer le lien communautaire et accompagner une étape essentielle de la vie.</p>
  </div>
</div>

{{-- ===================== FEUILLE DE ROUTE ===================== --}}
<div class="page">
  <div class="kicker">Trajectoire</div>
  <div class="h-sec">Feuille de route &amp; évolution</div>
  <div class="h-rule"></div>

  <div class="phase"><div class="phase-dot"></div><div class="when">Phase 1 — Lancement</div><h3>Mise en ligne &amp; première communauté</h3><p>Ouverture au Sénégal, acquisition des premiers membres, activation de la vérification et de la communauté.</p></div>
  <div class="phase"><div class="phase-dot"></div><div class="when">Phase 2 — Monétisation</div><h3>Abonnements &amp; boosts</h3><p>Déploiement complet du premium et des paiements mobiles, optimisation de la conversion et des mises en relation.</p></div>
  <div class="phase"><div class="phase-dot"></div><div class="when">Phase 3 — Croissance</div><h3>Notoriété &amp; rétention</h3><p>Marketing communautaire, parrainage, premiers témoignages d'unions, partenariats et événements.</p></div>
  <div class="phase"><div class="phase-dot"></div><div class="when">Phase 4 — Expansion</div><h3>Diaspora &amp; nouveaux services</h3><p>Ouverture à la diaspora, applications mobiles natives, accompagnement personnalisé, visioconférence et services premium.</p></div>
  <div class="phase" style="border-color:#fff;"><div class="phase-dot"></div><div class="when">Vision</div><h3>La référence matrimoniale</h3><p>Devenir la maison matrimoniale de confiance de toute la communauté sénégalaise dans le monde.</p></div>
</div>

{{-- ===================== APPEL À L'ACTION ===================== --}}
<div class="page">
  <div class="kicker">Rejoignez l'aventure</div>
  <div class="h-sec">Pourquoi nous rejoindre</div>
  <div class="h-rule"></div>

  <table class="grid"><tr>
    <td width="50%"><div class="card" style="margin-bottom:10px;"><h3>Pour un partenaire / acteur</h3><p>Participer à un projet à fort impact culturel et social, contribuer à la croissance d'une communauté fidèle et porteuse de sens.</p></div></td>
    <td width="50%"><div class="card" style="margin-bottom:10px;"><h3>Pour un investisseur</h3><p>Se positionner tôt sur un marché mal servi, avec un modèle de revenus récurrents clair et une vision d'expansion régionale et diaspora.</p></div></td>
  </tr></table>

  <div class="cta">
    <h3>Construisons ensemble la maison matrimoniale de référence</h3>
    <p>Nous recherchons des partenaires et investisseurs qui partagent notre vision d'unions sincères et durables.
    Discutons de la manière dont vous pouvez contribuer à cette aventure — et en bénéficier.</p>
    <p style="margin-top:12px;color:#caa552;"><b>Prochaine étape :</b> un rendez-vous pour présenter la démonstration en direct, les chiffres et les modalités de collaboration.</p>
  </div>

  <table style="margin-top:22px;">
    <tr>
      <td style="text-align:center;color:#6f695c;font-size:11px;line-height:1.8;">
        <b style="color:#1a1712;font-family:'DejaVu Serif',serif;font-size:15px;">TàakDiàkka</b><br/>
        La rencontre bénie · l'union sincère<br/>
        Contact : pointcom93@gmail.com
      </td>
    </tr>
  </table>

  <div class="footer-note">
    Document confidentiel — TàakDiàkka © {{ now()->year }}. Toute reproduction sans autorisation est interdite.
  </div>
</div>

</body>
</html>
