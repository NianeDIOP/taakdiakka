<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>@yield('title', 'Mon espace — TàakDiàkka')</title>
<link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png" />
<link rel="apple-touch-icon" href="{{ asset('img/logo-mark.png') }}" />
<meta name="theme-color" content="#f7f3ea" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/taakdiakka.css') }}" />
@stack('styles')
</head>
<body>

<a href="#main" class="skip-link">Aller au contenu</a>

@include('partials.icons')

@php $is = fn ($r) => request()->routeIs($r) ? 'active' : ''; @endphp

<nav class="mnav">
  <div class="mnav-inner">
    <a href="{{ route('dashboard') }}" class="brand">
      <img src="{{ \App\Models\Setting::logo() }}" alt="" width="36" height="36" />
      <span class="wm">Tàak<b>Diàkka</b></span>
    </a>

    <div class="mnav-links">
      <a href="{{ route('dashboard') }}" class="{{ $is('dashboard') }}"><svg class="ic"><use href="#i-grid"/></svg>Accueil</a>
      <a href="{{ route('communaute') }}" class="{{ request()->routeIs('communaute','communaute.show') ? 'active' : '' }}"><svg class="ic"><use href="#i-chat"/></svg>Communauté</a>
      <a href="{{ route('members.discover') }}" class="{{ request()->routeIs('members.discover','members.show') ? 'active' : '' }}"><svg class="ic"><use href="#i-search"/></svg>Découvrir</a>
      <a href="{{ route('friends.index') }}" class="{{ $is('friends.index') }}"><svg class="ic"><use href="#i-rings"/></svg>Demandes @if($navPendingFriends)<span class="nav-badge">{{ $navPendingFriends }}</span>@endif</a>
      <a href="{{ route('favoris') }}" class="{{ $is('favoris') }}"><svg class="ic"><use href="#i-heart"/></svg>Favoris</a>
      <a href="{{ route('visitors') }}" class="{{ $is('visitors') }}"><svg class="ic"><use href="#i-eye"/></svg>Visiteurs</a>
    </div>

    <div class="mnav-right">
      <a href="{{ route('notifications.index') }}" class="mnav-ic {{ $is('notifications.index') }}" aria-label="Notifications" title="Notifications">
        <svg class="ic"><use href="#i-bell"/></svg>
        @if($navUnreadNotifs)<span class="ic-badge">{{ $navUnreadNotifs > 9 ? '9+' : $navUnreadNotifs }}</span>@endif
      </a>
      <a href="{{ route('messages') }}" class="mnav-ic {{ $is('messages') }}" aria-label="Messages" title="Messages">
        <svg class="ic"><use href="#i-message"/></svg>
        @if($navUnreadMessages)<span class="ic-badge">{{ $navUnreadMessages > 9 ? '9+' : $navUnreadMessages }}</span>@endif
      </a>

      @php $acct = request()->routeIs('profile.*','demandes.mine','matchs','verification','settings','tarifs') ? 'active' : ''; @endphp
      <div class="mdrop" id="mdrop">
        <button class="mdrop-btn {{ $acct }}" id="mdropBtn" aria-label="Mon compte">
          <svg class="ic sm"><use href="#i-user"/></svg><span class="mdrop-name">{{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</span>
          <svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
        </button>
        <div class="mdrop-menu">
          <a href="{{ route('profile.show') }}" class="{{ $is('profile.*') }}"><svg class="ic"><use href="#i-user"/></svg>Mon profil</a>
          <a href="{{ route('matchs') }}" class="{{ $is('matchs') }}"><svg class="ic"><use href="#i-heart"/></svg>Matchs</a>
          <a href="{{ route('demandes.mine') }}" class="{{ request()->routeIs('demandes.mine') ? 'active' : '' }}"><svg class="ic"><use href="#i-rings"/></svg>Ma demande</a>
          <a href="{{ route('friends.index') }}" class="{{ $is('friends.index') }}"><svg class="ic"><use href="#i-rings"/></svg>Demandes d'amis @if($navPendingFriends)<span class="nav-badge">{{ $navPendingFriends }}</span>@endif</a>
          <a href="{{ route('community.saved') }}" class="{{ $is('community.saved') }}"><svg class="ic"><use href="#i-bookmark"/></svg>Enregistrements</a>
          <div class="sep"></div>
          <a href="{{ route('verification') }}" class="{{ $is('verification') }}"><svg class="ic"><use href="#i-verified"/></svg>Vérification</a>
          <a href="{{ route('subscription.mine') }}" class="{{ request()->routeIs('subscription.mine','tarifs') ? 'active' : '' }}"><svg class="ic"><use href="#i-spark"/></svg>Mon abonnement</a>
          <a href="{{ route('coins.shop') }}" class="{{ request()->routeIs('coins.*') ? 'active' : '' }}" style="display:flex;justify-content:space-between;align-items:center"><span><svg class="ic"><use href="#i-spark"/></svg>Pièces d'or</span><span class="coins-bal">🪙 {{ auth()->user()->coins_balance }}</span></a>
          <a href="{{ route('settings') }}" class="{{ $is('settings') }}"><svg class="ic"><use href="#i-grid"/></svg>Paramètres</a>
          @if(auth()->user()->isAdminUser())
            <a href="{{ route('admin.dashboard') }}"><svg class="ic"><use href="#i-grid"/></svg>Administration</a>
          @endif
          <div class="sep"></div>
          <a href="{{ route('home') }}"><svg class="ic"><use href="#i-arrow"/></svg>Retour au site</a>
          <form action="{{ route('logout') }}" method="POST">@csrf
            <button type="submit"><svg class="ic"><use href="#i-rings"/></svg>Se déconnecter</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</nav>

@if(session('status'))
  <div class="flash" data-flash="success">{{ session('status') }}</div>
@endif

@php
  $isPremiumUser = auth()->user()->isAdminUser() || auth()->user()->hasActiveSubscription();
  $showUpgrade   = \App\Support\FeatureGate::monetizationEnabled() && ! $isPremiumUser;
@endphp
@if($showUpgrade)
  <div class="upbar" id="upbar">
    <span class="upbar-txt"><svg class="ic sm"><use href="#i-spark"/></svg>Passez <b>Premium</b> pour envoyer des demandes d'amis et discuter librement.</span>
    <a href="{{ route('tarifs') }}" class="upbar-cta">Voir les formules ✨</a>
    <button type="button" class="upbar-x" aria-label="Masquer" onclick="this.parentElement.style.display='none';try{sessionStorage.setItem('upbarHidden','1')}catch(e){}">×</button>
  </div>
  <script>try{if(sessionStorage.getItem('upbarHidden')){var _u=document.getElementById('upbar');if(_u)_u.style.display='none';}}catch(e){}</script>
@endif

<main class="mwrap" id="main">
  @yield('content')
</main>

@include('partials.online-fab')

{{-- Dark mode toggle --}}
<button type="button" class="dark-toggle" id="darkToggle" aria-label="Mode sombre" title="Mode sombre">🌙</button>

{{-- Barre d'onglets mobile (façon application) --}}
<nav class="tabbar">
  <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}"><svg class="ic"><use href="#i-grid"/></svg><span>Accueil</span></a>
  <a href="{{ route('communaute') }}" class="{{ request()->routeIs('communaute','communaute.show','community.saved') ? 'on' : '' }}"><svg class="ic"><use href="#i-chat"/></svg><span>Communauté</span></a>
  <a href="{{ route('members.discover') }}" class="{{ request()->routeIs('members.discover','members.show') ? 'on' : '' }}"><svg class="ic"><use href="#i-search"/></svg><span>Découvrir</span></a>
  <a href="{{ route('messages') }}" class="{{ request()->routeIs('messages') ? 'on' : '' }}"><svg class="ic"><use href="#i-message"/></svg>@if($navUnreadMessages)<span class="tabbar-badge">{{ $navUnreadMessages > 9 ? '9+' : $navUnreadMessages }}</span>@endif<span>Message</span></a>
  <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*','settings','subscription.mine','verification','demandes.mine','matchs','friends.index') ? 'on' : '' }}"><svg class="ic"><use href="#i-user"/></svg><span>Profil</span></a>
</nav>

<script src="{{ asset('js/taakdiakka.js') }}"></script>
<script>
  (function () {
    const md = document.getElementById('mdrop');
    if (!md) return;
    document.getElementById('mdropBtn').addEventListener('click', (e) => {
      e.stopPropagation();
      md.classList.toggle('open');
    });
    document.addEventListener('click', () => md.classList.remove('open'));
  })();
</script>

@stack('scripts')
<script>
(function(){
  var btn=document.getElementById('darkToggle');if(!btn)return;
  var dk=localStorage.getItem('dark')==='1';
  function apply(on){document.body.classList.toggle('dark',on);btn.textContent=on?'☀️':'🌙';localStorage.setItem('dark',on?'1':'0');}
  apply(dk);
  btn.addEventListener('click',function(){dk=!dk;apply(dk);});
})();
</script>
</body>
</html>
