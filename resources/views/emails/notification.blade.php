@extends('emails._layout')

@section('content')
  <h1 style="font-family:Georgia,'Times New Roman',serif;font-size:24px;font-weight:500;margin:0 0 16px;color:#1a1712">{{ $heading }}</h1>
  @foreach($lines as $line)
    <p style="font-size:15px;line-height:1.7;color:#3a352c;margin:0 0 16px">{{ $line }}</p>
  @endforeach
  @if($ctaLabel && $ctaUrl)
    <table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:8px"><tr><td style="background:#a06d12">
      <a href="{{ $ctaUrl }}" style="display:inline-block;padding:13px 26px;color:#fff;text-decoration:none;font-size:14px;font-weight:600;letter-spacing:.3px">{{ $ctaLabel }}</a>
    </td></tr></table>
  @endif
@endsection
