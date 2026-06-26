@extends('layouts.admin')

@section('title', 'Modules — Administration')
@section('heading', 'Modules & règles premium')

@section('content')

<form method="POST" action="{{ route('admin.modules.save') }}">
  @csrf

  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-grid"/></svg>Modules de la plateforme</h3>
    <p style="color:var(--muted);font-size:.86rem;margin-bottom:18px">Activez ou désactivez des sections entières du site. Désactivé, le module devient inaccessible aux membres.</p>

    <div style="display:flex;flex-direction:column">
      @foreach($modules as $m)
        <label class="adm-toggle-row">
          <span class="adm-toggle-label">{{ $m['label'] }}</span>
          <span class="adm-switch">
            <input type="checkbox" name="module_{{ $m['key'] }}" value="1" @checked($m['enabled']) />
            <span class="adm-switch-track"></span>
          </span>
        </label>
      @endforeach
    </div>
  </div>

  <div class="adm-card" style="border-color:var(--gold)">
    <h3><svg class="ic"><use href="#i-spark"/></svg>Monétisation</h3>
    <p style="color:var(--muted);font-size:.86rem;margin-bottom:6px">Interrupteur maître. Tant qu'il est désactivé, la plateforme est entièrement gratuite et ouverte. Activez-le une fois les abonnements et le paiement en place pour appliquer les restrictions ci-dessous.</p>
    <label class="adm-toggle-row">
      <span class="adm-toggle-label">Appliquer les restrictions premium
        <small style="display:block;color:var(--muted);font-weight:400">@if($monetization)<b style="color:var(--ink)">Activée</b> — les règles ci-dessous s'appliquent@else Désactivée — accès complet pour tous @endif</small>
      </span>
      <span class="adm-switch">
        <input type="checkbox" name="monetization" value="1" @checked($monetization) />
        <span class="adm-switch-track"></span>
      </span>
    </label>
  </div>

  <div class="adm-card">
    <h3><svg class="ic"><use href="#i-spark"/></svg>Règles de la version gratuite</h3>
    <p style="color:var(--muted);font-size:.86rem;margin-bottom:18px">Définissez ce que les membres gratuits peuvent faire et leurs limites. Les abonnés premium ne sont pas concernés par ces limites. Ces règles ne s'appliquent que si la monétisation est activée ci-dessus.</p>

    <div style="display:flex;flex-direction:column">
      @foreach($rules as $r)
        <label class="adm-toggle-row">
          <span class="adm-toggle-label">{{ $r['label'] }}
            @if($r['type'] === 'int')<small style="display:block;color:var(--muted);font-weight:400">0 = aucun · laissez élevé pour « illimité »</small>@endif
          </span>
          @if($r['type'] === 'bool')
            <span class="adm-switch">
              <input type="checkbox" name="rule_{{ $r['key'] }}" value="1" @checked($r['value']) />
              <span class="adm-switch-track"></span>
            </span>
          @else
            <input type="number" name="rule_{{ $r['key'] }}" value="{{ $r['value'] }}" min="0" max="9999"
              style="width:100px;border:1px solid var(--line);background:var(--ivory);padding:9px 12px;font-family:var(--font-sans);font-size:.9rem" />
          @endif
        </label>
      @endforeach
    </div>
  </div>

  <button class="adm-btn solid" type="submit"><svg class="ic"><use href="#i-check"/></svg>Enregistrer les paramètres</button>
</form>

@endsection
