<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>@yield('title', 'Administration — TàakDiàkka')</title>
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/taakdiakka.css') }}" />
@stack('styles')
</head>
<body class="admin-body">

@include('partials.icons')

@php $is = fn (...$r) => request()->routeIs(...$r) ? 'on' : ''; @endphp

<div class="adm">
  <aside class="adm-side" id="admSide">
    <a href="{{ route('admin.dashboard') }}" class="adm-brand">
      <img src="{{ \App\Models\Setting::logo() }}" alt="" width="34" height="34" />
      <span>Tàak<b>Diàkka</b><small>Administration</small></span>
    </a>

    <nav class="adm-nav">
      <span class="adm-nav-label">Pilotage</span>
      <a href="{{ route('admin.dashboard') }}" class="{{ $is('admin.dashboard') }}"><svg class="ic"><use href="#i-grid"/></svg>Tableau de bord</a>
      <a href="{{ route('admin.users.index') }}" class="{{ $is('admin.users.*') }}"><svg class="ic"><use href="#i-user"/></svg>Utilisateurs</a>
      <a href="{{ route('admin.moderation') }}" class="{{ $is('admin.moderation*') }}"><svg class="ic"><use href="#i-flag"/></svg>Modération
        @if(($admPendingReports ?? 0) > 0)<span class="adm-badge">{{ $admPendingReports }}</span>@endif
      </a>
      <a href="{{ route('admin.community') }}" class="{{ $is('admin.community*') }}"><svg class="ic"><use href="#i-chat"/></svg>Communauté</a>
      <a href="{{ route('admin.blocks') }}" class="{{ $is('admin.blocks*') }}"><svg class="ic"><use href="#i-x"/></svg>Blocages</a>

      <span class="adm-nav-label">Monétisation</span>
      @if(auth()->user()->isSuperAdmin())
      <a href="{{ route('admin.billing.plans') }}" class="{{ $is('admin.billing.plans') }}"><svg class="ic"><use href="#i-spark"/></svg>Formules &amp; boosts</a>
      <a href="{{ route('admin.billing.subscriptions') }}" class="{{ $is('admin.billing.subscriptions') }}"><svg class="ic"><use href="#i-rings"/></svg>Abonnements</a>
      <a href="{{ route('admin.billing.payment') }}" class="{{ $is('admin.billing.payment') }}"><svg class="ic"><use href="#i-verified"/></svg>Paiement</a>
      @endif

      <span class="adm-nav-label">Configuration</span>
      <a href="{{ route('admin.content') }}" class="{{ $is('admin.content*') }}"><svg class="ic"><use href="#i-heart"/></svg>Contenu &amp; témoignages</a>
      @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.modules') }}" class="{{ $is('admin.modules*') }}"><svg class="ic"><use href="#i-spark"/></svg>Modules &amp; premium</a>
        <a href="{{ route('admin.settings') }}" class="{{ $is('admin.settings*') }}"><svg class="ic"><use href="#i-search"/></svg>Paramètres &amp; SEO</a>
        <a href="{{ route('admin.pages') }}" class="{{ $is('admin.pages*') }}"><svg class="ic"><use href="#i-pin"/></svg>Pages légales</a>
        <a href="{{ route('admin.logs') }}" class="{{ $is('admin.logs') }}"><svg class="ic"><use href="#i-bell"/></svg>Journal d'activité</a>
      @endif
    </nav>
  </aside>

  <div class="adm-main">
    <header class="adm-top">
      <button class="adm-burger" id="admBurger" aria-label="Menu"><svg class="ic"><use href="#i-grid"/></svg></button>
      <div class="adm-top-title">@yield('heading', 'Administration')</div>

      <div class="adm-usermenu" id="admUserMenu">
        <button class="adm-usermenu-btn" id="admUserBtn" aria-label="Compte">
          <span class="adm-usermenu-id">
            <span class="adm-usermenu-name">{{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</span>
            <span class="adm-role">{{ \App\Models\User::ROLES[auth()->user()->role] ?? 'Admin' }}</span>
          </span>
          <span class="av s" data-av="{{ \Illuminate\Support\Str::substr(auth()->user()->name, 0, 1) }}"></span>
          <svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
        </button>
        <div class="adm-usermenu-pop">
          <div class="adm-usermenu-head">
            <b>{{ auth()->user()->name }}</b>
            <small>{{ auth()->user()->email }}</small>
          </div>
          @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.settings') }}"><svg class="ic"><use href="#i-search"/></svg>Paramètres &amp; SEO</a>
            <a href="{{ route('admin.modules') }}"><svg class="ic"><use href="#i-grid"/></svg>Modules &amp; premium</a>
            <a href="{{ route('admin.logs') }}"><svg class="ic"><use href="#i-bell"/></svg>Journal d'activité</a>
            <div class="sep"></div>
          @endif
          <a href="{{ route('home') }}" target="_blank"><svg class="ic"><use href="#i-arrow"/></svg>Voir le site public</a>
          <form action="{{ route('logout') }}" method="POST">@csrf
            <button type="submit"><svg class="ic"><use href="#i-rings"/></svg>Se déconnecter</button>
          </form>
        </div>
      </div>
    </header>

    @if(session('status'))
      <div class="flash adm-flash">{{ session('status') }}</div>
    @endif
    @if($errors->any())
      <div class="flash adm-flash err">{{ $errors->first() }}</div>
    @endif

    <main class="adm-content">
      @yield('content')
    </main>
  </div>
</div>

<script>
  (function () {
    const burger = document.getElementById('admBurger');
    const side = document.getElementById('admSide');
    if (burger && side) burger.addEventListener('click', () => side.classList.toggle('open'));

    const menu = document.getElementById('admUserMenu');
    const btn = document.getElementById('admUserBtn');
    if (menu && btn) {
      btn.addEventListener('click', (e) => { e.stopPropagation(); menu.classList.toggle('open'); });
      document.addEventListener('click', () => menu.classList.remove('open'));
    }
  })();
</script>
@stack('scripts')
</body>
</html>
