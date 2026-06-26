@extends('layouts.app')

@section('title', $post->theme . ' — Communauté · TàakDiàkka')
@section('description', \Illuminate\Support\Str::limit(strip_tags($post->body), 150))

@section('content')

<section id="communaute" class="sec--alt"><div class="wrap" style="padding-top:clamp(120px,15vw,170px)">
  <a href="{{ route('communaute') }}" class="lnk" style="margin-bottom:36px">
    <svg class="ic sm" style="transform:rotate(180deg)"><use href="#i-arrow"/></svg>Retour à la communauté
  </a>

  <div style="max-width:760px;margin:0 auto">
    @include('partials.post-card', ['p' => $post, 'full' => true])
  </div>

  @if($similaires->count())
    <div class="sec-head reveal" style="margin:clamp(64px,9vw,100px) auto 44px">
      <span class="label center">À lire aussi</span>
      <h2>Discussions <em>similaires</em></h2>
    </div>
    <div style="max-width:760px;margin:0 auto">
      @foreach($similaires as $p2)
        @include('partials.post-card', ['p' => $p2, 'stagger' => $loop->index % 3])
      @endforeach
    </div>
  @endif
</div></section>

@push('scripts')
<script>window.TD_AUTH = @json(auth()->check()); window.TD_THEME = 'Tout'; window.TD_TAG = null;</script>
<script src="{{ asset('js/community.js') }}"></script>
@endpush
@endsection
