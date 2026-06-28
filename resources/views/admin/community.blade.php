@extends('layouts.admin')

@section('title', 'Communauté — Administration')
@section('heading', 'Communauté')

@section('content')

<div class="kpi-grid" style="margin-bottom:24px">
  <div class="kpi">
    <div class="kpi-label">Publications</div>
    <div class="kpi-val">{{ $stats['posts'] }}</div>
    <div class="kpi-sub">+{{ $stats['today'] }} aujourd'hui</div>
  </div>
  <div class="kpi">
    <div class="kpi-label">Commentaires</div>
    <div class="kpi-val">{{ $stats['comments'] }}</div>
  </div>
</div>

<div class="tabs" style="margin-bottom:18px">
  <a href="{{ route('admin.community', ['tab' => 'posts']) }}" class="tab {{ $tab === 'posts' ? 'active' : '' }}">Publications</a>
  <a href="{{ route('admin.community', ['tab' => 'comments']) }}" class="tab {{ $tab === 'comments' ? 'active' : '' }}">Commentaires</a>
</div>

<form method="GET" class="adm-filters" style="margin-bottom:18px">
  <input type="hidden" name="tab" value="{{ $tab }}" />
  <div class="fld" style="flex:1;min-width:220px">
    <label>Recherche</label>
    <input type="text" name="q" value="{{ $q }}" placeholder="Rechercher dans le contenu…" />
  </div>
  @if($tab === 'posts')
    <div class="fld">
      <label>Thème</label>
      <select name="theme">
        <option value="">Tous</option>
        @foreach(['confession', 'conseil', 'temoignage', 'question'] as $t)
          <option value="{{ $t }}" @selected(request('theme') === $t)>{{ ucfirst($t) }}</option>
        @endforeach
      </select>
    </div>
  @endif
  <button class="adm-btn solid" type="submit"><svg class="ic"><use href="#i-search"/></svg>Filtrer</button>
</form>

<div class="adm-card" style="padding:0;overflow-x:auto">
  @if($tab === 'posts')
    <table class="adm-table">
      <thead><tr><th>Auteur</th><th>Thème</th><th>Contenu</th><th>Publié</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $post)
          @php
            $pAuthor = $post->author;
            $pProfile = $pAuthor?->profile;
            $pAv = $pProfile && $pProfile->photo ? pathinfo($pProfile->photo, PATHINFO_FILENAME) : null;
            $pName = $post->author_name ?? $pAuthor?->name ?? 'Anonyme';
          @endphp
          <tr>
            <td>
              <div class="u-cell">
                @if($pAv)
                  <span class="av s photo" style="background-image:url('{{ asset('img/'.$pAv.'.webp') }}')"></span>
                @else
                  <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($pName, 0, 1) }}"></span>
                @endif
                <div>
                  <b>{{ $pName }}</b>
                  @if($post->is_anonymous)<small style="color:var(--muted)">Anonyme</small>@endif
                </div>
              </div>
            </td>
            <td><span class="adm-tag">{{ $post->theme_emoji }} {{ ucfirst($post->theme ?? '—') }}</span></td>
            <td style="max-width:340px"><p style="margin:0;font-size:.88rem;color:var(--ink);line-height:1.4">{{ \Illuminate\Support\Str::limit($post->body, 100) }}</p></td>
            <td>{{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
            <td>
              <div style="display:flex;gap:6px;align-items:center">
                <a href="{{ route('communaute.show', $post) }}" target="_blank" class="adm-btn"><svg class="ic sm"><use href="#i-eye"/></svg></a>
                <form method="POST" action="{{ route('admin.community.post.delete', $post) }}" onsubmit="return confirm('Supprimer cette publication ?');">
                  @csrf @method('DELETE')
                  <button class="adm-btn danger" type="submit"><svg class="ic sm"><use href="#i-x"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px">Aucune publication.</td></tr>
        @endforelse
      </tbody>
    </table>
  @else
    <table class="adm-table">
      <thead><tr><th>Auteur</th><th>Commentaire</th><th>Publication</th><th>Date</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $comment)
          @php
            $cUser = $comment->user;
            $cProfile = $cUser?->profile;
            $cAv = $cProfile && $cProfile->photo ? pathinfo($cProfile->photo, PATHINFO_FILENAME) : null;
          @endphp
          <tr>
            <td>
              <div class="u-cell">
                @if($cAv)
                  <span class="av s photo" style="background-image:url('{{ asset('img/'.$cAv.'.webp') }}')"></span>
                @else
                  <span class="av s" data-av="{{ \Illuminate\Support\Str::substr($cUser?->name ?? '?', 0, 1) }}"></span>
                @endif
                <div><b>{{ $cUser?->name ?? 'Supprimé' }}</b></div>
              </div>
            </td>
            <td style="max-width:300px"><p style="margin:0;font-size:.88rem;color:var(--ink);line-height:1.4">{{ \Illuminate\Support\Str::limit($comment->body, 80) }}</p></td>
            <td>
              @if($comment->post)
                <a href="{{ route('communaute.show', $comment->post) }}" target="_blank" class="lnk">{{ \Illuminate\Support\Str::limit($comment->post->body, 40) }}</a>
              @else
                <span style="color:var(--muted)">Supprimée</span>
              @endif
            </td>
            <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
            <td>
              <form method="POST" action="{{ route('admin.community.comment.delete', $comment) }}" onsubmit="return confirm('Supprimer ce commentaire ?');">
                @csrf @method('DELETE')
                <button class="adm-btn danger" type="submit"><svg class="ic sm"><use href="#i-x"/></svg></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px">Aucun commentaire.</td></tr>
        @endforelse
      </tbody>
    </table>
  @endif
</div>

@if($items->hasPages())
  <nav class="pager">
    @if($items->onFirstPage())<span class="pager-btn disabled">Précédent</span>
    @else<a class="pager-btn" href="{{ $items->previousPageUrl() }}">Précédent</a>@endif
    <span class="pager-info">Page {{ $items->currentPage() }} / {{ $items->lastPage() }} · {{ $items->total() }} résultats</span>
    @if($items->hasMorePages())<a class="pager-btn" href="{{ $items->nextPageUrl() }}">Suivant</a>
    @else<span class="pager-btn disabled">Suivant</span>@endif
  </nav>
@endif

@endsection
