@php $onDark = request()->routeIs('histoires'); @endphp
<nav class="nav {{ $onDark ? 'nav--dark' : '' }}" id="nav">
  <div class="nav-inner">
    <a href="{{ url('/') }}" class="brand" aria-label="TàakDiàkka — accueil">
      <picture>
        <source srcset="{{ asset('img/logo-mark.webp') }}" type="image/webp" />
        <img src="{{ \App\Models\Setting::logo() }}" alt="TàakDiàkka" width="50" height="50" fetchpriority="high" />
      </picture>
      <span class="wm">Tàak<b>Diàkka</b></span>
    </a>
    <div class="nav-links" id="navlinks">
      @auth
        <a href="{{ route('members.discover') }}"><svg class="ic sm"><use href="#i-search"/></svg>Membres</a>
      @endauth
      <a href="{{ route('communaute') }}"><svg class="ic sm"><use href="#i-chat"/></svg>Communauté</a>
      <a href="{{ route('histoires') }}"><svg class="ic sm"><use href="#i-rings"/></svg>Histoires</a>
      @auth
        <a href="{{ route('dashboard') }}" class="btn btn-primary"><svg class="ic sm"><use href="#i-user"/></svg>Mon espace</a>
      @else
        <a href="{{ route('tarifs') }}"><svg class="ic sm"><use href="#i-spark"/></svg>Abonnements</a>
        <a href="{{ route('login') }}"><svg class="ic sm"><use href="#i-user"/></svg>Connexion</a>
        <a href="{{ route('register') }}" class="btn btn-primary"><svg class="ic sm"><use href="#i-heart"/></svg>Rejoindre</a>
      @endauth
    </div>
    <button class="burger" id="burger"><span></span><span></span><span></span></button>
  </div>
</nav>
