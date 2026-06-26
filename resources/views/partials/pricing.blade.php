@php
  $plans = $plans ?? \App\Models\Plan::active()->orderBy('sort_order')->get();
  $current = $current ?? (auth()->check() ? auth()->user()->activeSubscription() : null);
  $currentPlanId = $current?->plan_id;
  $featuredId = optional($plans->where('is_premium', true)->sortBy('price')->first())->id;
@endphp
<section id="tarifs"><div class="wrap">
  <div class="sec-head reveal">
    <span class="label center">Abonnements</span>
    <h2>Des formules <em>claires</em></h2>
    <p>Commencez gratuitement. Passez au premium quand vous êtes prêt(e) à aller plus loin.</p>
  </div>
  <div class="pricing">
    @foreach($plans as $plan)
      @php
        $isFeatured = $plan->id === $featuredId;
        $isCurrent = $plan->id === $currentPlanId;
      @endphp
      <article class="plan reveal {{ $isFeatured ? 'featured on-dark' : '' }}" @if(!$loop->first) data-d="{{ $loop->index }}" @endif>
        @if($isCurrent)
          <div class="ptag" style="background:#1f7a4d">Votre formule</div>
        @elseif($isFeatured)
          <div class="ptag">Recommandé</div>
        @endif
        <div class="pname">{{ $plan->name }}</div>
        <div class="price">{{ $plan->is_free ? '0' : number_format($plan->price, 0, ',', ' ') }}<span>FCFA</span></div>
        <div class="pper">
          @if($plan->compare_at_price)<s style="opacity:.55">{{ $plan->compare_label }}</s> @endif
          {{ $plan->is_free ? 'Gratuit, pour toujours' : trim(str_replace('/', '', $plan->period_label)) }}@if($plan->tagline) · {{ $plan->tagline }}@endif
        </div>
        <ul>
          @foreach($plan->features ?? [] as $feat)
            <li><svg class="ic"><use href="#i-check"/></svg>{{ $feat }}</li>
          @endforeach
        </ul>
        @if($isCurrent)
          <span class="btn btn-line" style="pointer-events:none;opacity:.7">Formule active ✓</span>
        @elseif($plan->is_free)
          @guest<a href="{{ route('register') }}" class="btn btn-line">Commencer</a>
          @else<span class="btn btn-line" style="pointer-events:none;opacity:.7">Incluse</span>@endguest
        @else
          @auth
            <form method="POST" action="{{ route('subscribe.checkout', $plan) }}">@csrf
              <button type="submit" class="btn {{ $isFeatured ? 'btn-primary' : 'btn-line' }}" style="width:100%">Choisir {{ $plan->name }}</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="btn {{ $isFeatured ? 'btn-primary' : 'btn-line' }}">Choisir {{ $plan->name }}</a>
          @endauth
        @endif
      </article>
    @endforeach
  </div>

  @isset($boosts)
  @if($boosts->count())
    <div class="sec-head reveal" style="margin-top:64px">
      <span class="label center">Boosts</span>
      <h2>Soyez <em>remarqué(e)</em></h2>
      <p>Mettez votre profil en avant pour multiplier les visites, indépendamment de votre formule.</p>
    </div>
    <div class="boost-row">
      @foreach($boosts as $b)
        <article class="boost-card reveal" @if(!$loop->first) data-d="{{ $loop->index }}" @endif>
          <div class="pname">{{ $b->name }}</div>
          <div class="price sm">{{ number_format($b->price, 0, ',', ' ') }}<span>FCFA</span></div>
          <div class="pper">{{ $b->duration_days }} jour{{ $b->duration_days > 1 ? 's' : '' }} de visibilité</div>
          @auth
            <form method="POST" action="{{ route('boost.checkout', $b) }}">@csrf
              <button type="submit" class="btn btn-line" style="width:100%">Booster</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="btn btn-line">Booster</a>
          @endauth
        </article>
      @endforeach
    </div>
  @endif
  @endisset

  <p class="pay-note reveal">Paiement sécurisé · Wave · Orange Money · Free Money · Carte bancaire</p>
</div></section>
