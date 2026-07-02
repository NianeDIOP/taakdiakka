@extends('layouts.admin')

@section('title', 'Paramètres — Administration')
@section('heading', 'Paramètres de la plateforme')

@section('content')

<form method="POST" action="{{ route('admin.settings.save') }}" enctype="multipart/form-data" class="settings-layout">
  @csrf

  {{-- Sous-navigation latérale --}}
  <nav class="settings-nav">
    <a href="#identite">Identité &amp; marque</a>
    <a href="#acces">Inscriptions &amp; accès</a>
    <a href="#social">Réseaux sociaux</a>
    <a href="#seo">SEO &amp; analyses</a>
    <a href="#mail">E-mails</a>
    <a href="#pub">Publicité</a>
    <button class="adm-btn solid settings-save" type="submit"><svg class="ic"><use href="#i-check"/></svg>Enregistrer</button>
  </nav>

  <div class="settings-panels">

    {{-- Identité --}}
    <div class="adm-card" id="identite">
      <h3><svg class="ic"><use href="#i-verified"/></svg>Identité &amp; marque</h3>
      <div class="settings-logo-row">
        <div class="settings-logo-prev">
          <img src="{{ \App\Models\Setting::logo() }}" alt="Logo" />
        </div>
        <label class="adm-field" style="flex:1">
          <span>Logo (PNG, SVG, JPG — max 2 Mo)</span>
          <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" />
          <small style="color:var(--muted)">Utilisé dans l'en-tête, le pied de page et l'administration.</small>
        </label>
      </div>
      <div class="adm-form-grid" style="margin-top:14px">
        <label class="adm-field"><span>Nom du site</span><input type="text" name="site_name" value="{{ old('site_name', $values['site.name']) }}" required /></label>
        <label class="adm-field"><span>Slogan</span><input type="text" name="site_tagline" value="{{ old('site_tagline', $values['site.tagline']) }}" /></label>
        <label class="adm-field"><span>E-mail de contact</span><input type="email" name="contact_email" value="{{ old('contact_email', $values['site.contact_email']) }}" /></label>
        <label class="adm-field"><span>Téléphone</span><input type="text" name="contact_phone" value="{{ old('contact_phone', $values['site.contact_phone']) }}" /></label>
        <label class="adm-field" style="grid-column:1/-1"><span>Adresse</span><input type="text" name="address" value="{{ old('address', $values['site.address']) }}" placeholder="Dakar, Sénégal" /></label>
      </div>
    </div>

    {{-- Accès --}}
    <div class="adm-card" id="acces">
      <h3><svg class="ic"><use href="#i-user"/></svg>Inscriptions &amp; accès</h3>
      <label class="adm-toggle-row">
        <span class="adm-toggle-label">Inscriptions ouvertes
          <small style="display:block;color:var(--muted);font-weight:400">Désactivé, la page d'inscription affiche un message et n'accepte plus de nouveaux comptes.</small>
        </span>
        <span class="adm-switch"><input type="checkbox" name="registration_open" value="1" @checked($values['site.registration_open'])><span class="adm-switch-track"></span></span>
      </label>
      <label class="adm-toggle-row">
        <span class="adm-toggle-label">Mode maintenance
          <small style="display:block;color:var(--muted);font-weight:400">Affiche une page de maintenance aux visiteurs. Les administrateurs gardent l'accès.</small>
        </span>
        <span class="adm-switch"><input type="checkbox" name="maintenance" value="1" @checked($values['site.maintenance'])><span class="adm-switch-track"></span></span>
      </label>
    </div>

    {{-- Réseaux sociaux --}}
    <div class="adm-card" id="social">
      <h3><svg class="ic"><use href="#i-share"/></svg>Réseaux sociaux</h3>
      <div class="adm-form-grid">
        @foreach(['facebook' => 'Facebook', 'instagram' => 'Instagram', 'whatsapp' => 'WhatsApp', 'tiktok' => 'TikTok', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube'] as $net => $label)
          <label class="adm-field"><span>{{ $label }}</span><input type="text" name="social_{{ $net }}" value="{{ old('social_'.$net, $values['social.'.$net]) }}" placeholder="{{ $net === 'whatsapp' ? '+221…' : 'https://…' }}" /></label>
        @endforeach
      </div>
    </div>

    {{-- SEO --}}
    <div class="adm-card" id="seo">
      <h3><svg class="ic"><use href="#i-search"/></svg>SEO &amp; analyses</h3>
      <p style="color:var(--muted);font-size:.86rem;margin-bottom:16px">Injecté automatiquement dans les pages publiques.</p>
      <div class="adm-form-grid">
        <label class="adm-field" style="grid-column:1/-1"><span>Titre méta par défaut</span><input type="text" name="meta_title" value="{{ old('meta_title', $values['seo.meta_title']) }}" maxlength="120" /></label>
        <label class="adm-field" style="grid-column:1/-1"><span>Description méta</span><textarea name="meta_description" rows="2" maxlength="255">{{ old('meta_description', $values['seo.meta_description']) }}</textarea></label>
        <label class="adm-field" style="grid-column:1/-1"><span>Mots-clés (séparés par des virgules)</span><input type="text" name="keywords" value="{{ old('keywords', $values['seo.keywords']) }}" /></label>
        <label class="adm-field"><span>Google Analytics</span><input type="text" name="ga_id" value="{{ old('ga_id', $values['seo.ga_id']) }}" placeholder="G-XXXXXXXXXX" /></label>
        <label class="adm-field"><span>Meta Pixel</span><input type="text" name="pixel_id" value="{{ old('pixel_id', $values['seo.pixel_id']) }}" placeholder="123456789012345" /></label>
        <label class="adm-field" style="grid-column:1/-1"><span>Image de partage (Open Graph)
          @if($values['seo.og_image'])<a href="{{ asset('img/'.$values['seo.og_image']) }}" target="_blank" style="color:var(--gold)"> · voir l'actuelle</a>@endif
        </span><input type="file" name="og_image" accept="image/png,image/jpeg,image/webp" /></label>
      </div>
    </div>

    {{-- E-mails --}}
    <div class="adm-card" id="mail">
      <h3><svg class="ic"><use href="#i-message"/></svg>E-mails (expéditeur &amp; SMTP)</h3>
      <p style="color:var(--muted);font-size:.86rem;margin-bottom:16px">Paramètres d'envoi des e-mails transactionnels (confirmations, notifications). Renseignés ici, sans redéploiement.</p>
      <div class="adm-form-grid">
        <label class="adm-field"><span>Nom de l'expéditeur</span><input type="text" name="from_name" value="{{ old('from_name', $values['mail.from_name']) }}" /></label>
        <label class="adm-field"><span>E-mail expéditeur</span><input type="email" name="from_email" value="{{ old('from_email', $values['mail.from_email']) }}" placeholder="contact@taakdiakka.com" /></label>
        <label class="adm-field"><span>Serveur SMTP</span><input type="text" name="mail_host" value="{{ old('mail_host', $values['mail.host']) }}" placeholder="smtp.exemple.com" /></label>
        <label class="adm-field"><span>Port</span><input type="text" name="mail_port" value="{{ old('mail_port', $values['mail.port']) }}" placeholder="587" /></label>
        <label class="adm-field"><span>Utilisateur</span><input type="text" name="mail_username" value="{{ old('mail_username', $values['mail.username']) }}" autocomplete="off" /></label>
        <label class="adm-field"><span>Mot de passe</span><input type="password" name="mail_password" value="{{ old('mail_password', $values['mail.password']) }}" autocomplete="new-password" /></label>
        <label class="adm-field"><span>Chiffrement</span>
          <select name="mail_encryption">
            @foreach(['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'Aucun'] as $v => $l)
              <option value="{{ $v }}" @selected($values['mail.encryption'] === $v)>{{ $l }}</option>
            @endforeach
          </select>
        </label>
      </div>

      {{-- Test d'envoi (formulaire séparé via attribut form=) --}}
      <div style="margin-top:18px;padding-top:16px;border-top:1px solid var(--line)">
        <span class="adm-block-label">Tester l'envoi</span>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
          <input type="email" name="test_email" form="testEmailForm" required value="{{ auth()->user()->email }}"
            style="flex:1;min-width:220px;border:1px solid var(--line);background:var(--ivory);padding:9px 12px;font-family:var(--font-sans);font-size:.86rem" />
          <button type="submit" form="testEmailForm" class="adm-btn"><svg class="ic"><use href="#i-send"/></svg>Envoyer un test</button>
        </div>
        <small style="color:var(--muted)">Enregistrez d'abord vos réglages SMTP, puis envoyez un test pour vérifier qu'ils fonctionnent.</small>
      </div>
    </div>

    {{-- Publicité --}}
    <div class="adm-card" id="pub">
      <h3><svg class="ic"><use href="#i-spark"/></svg>Bannière publicitaire — accueil</h3>
      <p style="color:var(--muted);font-size:.86rem;margin-bottom:18px">
        Affichée entre le hero et la section "Un cercle protégé". Format recommandé : <strong>970 × 250 px</strong>.
      </p>

      <label class="adm-toggle-row" style="margin-bottom:18px">
        <span class="adm-toggle-label">Afficher la bannière
          <small style="display:block;color:var(--muted);font-weight:400">Désactivez pour masquer la pub sans supprimer l'image.</small>
        </span>
        <span class="adm-switch">
          <input type="checkbox" name="ad_banner_active" value="1" @checked($values['ad.banner_active'])>
          <span class="adm-switch-track"></span>
        </span>
      </label>

      <div class="adm-form-grid">
        <label class="adm-field" style="grid-column:1/-1">
          <span>Image publicitaire (PNG, JPG, WEBP — max 4 Mo)
            @if($values['ad.banner_image'])
              &nbsp;·&nbsp;<a href="{{ asset('img/'.$values['ad.banner_image']) }}" target="_blank" style="color:var(--gold)">voir l'actuelle</a>
            @endif
          </span>
          <input type="file" name="ad_banner_image" accept="image/png,image/jpeg,image/webp" />
          @if($values['ad.banner_image'])
            <div style="margin-top:10px">
              <img src="{{ asset('img/'.$values['ad.banner_image']) }}" alt="Bannière pub" style="max-width:100%;max-height:100px;object-fit:cover;border:1px solid var(--line)" />
            </div>
          @endif
        </label>

        <label class="adm-field">
          <span>Numéro de contact (sans espaces ni tirets)</span>
          <input type="text" name="ad_contact" value="{{ old('ad_contact', $values['ad.contact']) }}" placeholder="+221771234567" maxlength="30" />
        </label>

        <label class="adm-field">
          <span>Action du bouton CTA</span>
          <select name="ad_cta_type">
            <option value="whatsapp" @selected(($values['ad.cta_type'] ?? 'whatsapp') === 'whatsapp')>WhatsApp</option>
            <option value="call" @selected(($values['ad.cta_type'] ?? 'whatsapp') === 'call')>Appel téléphonique</option>
          </select>
        </label>

        <label class="adm-field">
          <span>Libellé du bouton</span>
          <input type="text" name="ad_cta_label" value="{{ old('ad_cta_label', $values['ad.cta_label']) }}" placeholder="Nous contacter" maxlength="60" />
        </label>
      </div>
    </div>

    <button class="adm-btn solid" type="submit" style="margin-top:6px"><svg class="ic"><use href="#i-check"/></svg>Enregistrer tous les paramètres</button>
  </div>
</form>

{{-- Formulaire séparé pour le test d'e-mail (référencé via form="testEmailForm") --}}
<form id="testEmailForm" method="POST" action="{{ route('admin.settings.testmail') }}">@csrf</form>

@endsection
