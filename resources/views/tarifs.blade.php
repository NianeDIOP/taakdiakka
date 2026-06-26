@extends('layouts.app')

@section('title', 'Abonnements — TàakDiàkka')
@section('description', 'Les formules TàakDiàkka : Découverte (gratuit), Premium mensuel et annuel. Boosts de visibilité. Paiement Wave, Orange Money, Free Money.')

@section('content')
<div style="height:clamp(60px,9vw,90px)"></div>
@include('partials.pricing', ['plans' => $plans ?? null, 'boosts' => $boosts ?? null, 'current' => $current ?? null])
@endsection
