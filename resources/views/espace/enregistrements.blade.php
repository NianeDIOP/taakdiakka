@extends('layouts.member')

@section('title', 'Mes enregistrements — TàakDiàkka')
@section('pagetitle', 'Enregistrements')

@section('content')
<div class="m-head">
  <span class="label">Pense-y</span>
  <h2>Mes <em>enregistrements</em></h2>
  <p>Les publications que vous avez gardées pour les relire plus tard.</p>
</div>

@if($posts->count())
  <div class="saved-feed">
    @foreach($posts as $p)
      @include('partials.post-card', ['p' => $p, 'stagger' => $loop->index % 3, 'savedIds' => $savedIds])
    @endforeach
  </div>
  <div style="margin-top:26px">{{ $posts->links() }}</div>
@else
  @include('partials.empty', [
    'icon' => 'bookmark',
    'title' => 'Aucun enregistrement',
    'text' => 'Touchez « Enregistrer » sous une publication pour la retrouver ici, à l\'abri. 🔖',
    'ctaUrl' => route('communaute'),
    'ctaLabel' => 'Aller à la communauté',
    'ctaIcon' => 'chat',
  ])
@endif
@endsection

@push('scripts')
<script>window.TD_AUTH = true; window.TD_THEME = 'Tout'; window.TD_TAG = null;</script>
<script src="{{ asset('js/community.js') }}"></script>
@endpush
