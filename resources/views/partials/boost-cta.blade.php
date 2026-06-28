{{-- Incitation à mettre son profil en avant (masquée si déjà boosté ou admin). --}}
@php $boostUser = auth()->user(); @endphp
@if($boostUser && ! $boostUser->isAdminUser() && ! $boostUser->isBoosted())
  <div class="boost-cta">
    <span class="boost-ic"><svg class="ic"><use href="#i-spark"/></svg></span>
    <div class="boost-body">
      <b>Mettez votre profil en avant 🚀</b>
      <p>Apparaissez en tête des résultats et recevez plus de visites et de demandes.</p>
    </div>
    <a href="{{ route('tarifs') }}#boosts" class="btn btn-line">Booster mon profil</a>
  </div>
@endif
