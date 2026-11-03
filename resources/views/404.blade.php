{{--
  404 Not Found page.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/404.blade.php -->
  @endif

  @php
    $quickLinks = [
      ['label' => 'Esileht', 'url' => home_url('/')],
      ['label' => 'Meeskonnad', 'url' => home_url('/meeskonnad')],
      ['label' => 'Noored', 'url' => home_url('/noored')],
      ['label' => 'Kalender', 'url' => home_url('/kalender')],
      ['label' => 'Uudised', 'url' => home_url('/uudised')],
      ['label' => 'Kontakt', 'url' => home_url('/kontakt')],
    ];
  @endphp

  <section class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-[var(--skduo-hero-bg)] px-4 py-16 text-center text-paper sm:px-6">
    <div class="pointer-events-none absolute inset-0 z-[1] bg-gradient-to-t from-[var(--skduo-hero-bg)] via-transparent to-transparent"></div>
    <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-green-mint/10 animate-float"></div>
    <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-green-mint/10 animate-float"></div>

    <div class="relative z-10 mx-auto flex max-w-2xl flex-col items-center">
      <span class="mb-6 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.2em] text-green-mint">
        <span class="h-px w-8 bg-green-mint"></span>
        Viga 404
        <span class="h-px w-8 bg-green-mint"></span>
      </span>

      <h1 class="font-display text-[clamp(6rem,22vw,12rem)] leading-none tracking-wide text-paper">
        4<span class="text-green-mint">0</span>4
      </h1>

      <h2 class="mt-2 font-serif-display text-[clamp(1.8rem,5vw,3rem)] leading-[0.95] tracking-normal">
        Seda lehte ei leitud.
      </h2>

      <p class="mt-6 max-w-lg text-base leading-relaxed text-paper/72 md:text-lg">
        Otsitav aadress on kas muutunud, eemaldatud või kirjutati vale URL. Kontrolli aadressi või jätka mõne alloleva lingi kaudu.
      </p>

      <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ home_url('/') }}" class="inline-flex items-center justify-center gap-3 rounded-full bg-green-mint px-6 py-3.5 text-xs font-extrabold uppercase tracking-widest text-ink shadow-cta-glow transition hover:-translate-y-0.5 hover:bg-lime">
          ← Avalehele
        </a>
        <a href="{{ home_url('/kontakt') }}" class="inline-flex items-center justify-center rounded-full border border-paper/35 px-6 py-3.5 text-xs font-extrabold uppercase tracking-widest text-paper transition hover:border-paper hover:bg-paper hover:text-ink">
          Võta ühendust
        </a>
      </div>

      <div class="mt-12 flex flex-wrap items-center justify-center gap-2 border-t border-paper/10 pt-8">
        @foreach($quickLinks as $link)
          <a href="{{ esc_url($link['url']) }}" class="rounded-full border border-paper/25 px-4 py-2 text-xs font-extrabold uppercase tracking-[0.14em] text-paper/70 transition hover:border-green-mint hover:text-green-mint">
            {{ $link['label'] }}
          </a>
        @endforeach
      </div>
    </div>
  </section>
@endsection
