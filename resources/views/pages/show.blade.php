@extends('layouts.app')

@section('title', $page->title.' — TàakDiàkka')
@section('description', \Illuminate\Support\Str::limit(strip_tags($page->body), 150))

@section('content')
<div style="height:clamp(60px,9vw,90px)"></div>
<section class="legal-page"><div class="wrap">
  <div class="sec-head reveal" style="text-align:left">
    <span class="label">Informations</span>
    <h2>{{ $page->title }}</h2>
  </div>
  <article class="legal-body reveal">
    {!! \Illuminate\Support\Str::markdown($page->body ?? '', ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
  </article>
  <p class="legal-updated">Dernière mise à jour : {{ $page->updated_at?->locale('fr')->isoFormat('D MMMM YYYY') }}</p>
</div></section>
<div style="height:clamp(40px,6vw,70px)"></div>
@endsection
