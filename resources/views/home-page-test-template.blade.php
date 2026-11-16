{{--
  Template Name: Home Page (3D Test)
  Experimental homepage hero — editorial kinetic-scroll style (landonorris.com):
  a pinned headline shrinks away to reveal a full-bleed photo, a small ambient
  3D volleyball spins beside it, and a horizontal scroll-rail follows below.
  Test only: assign to any page via Page Attributes → Template.
--}}
@extends('layouts.app')

@php
  $railCards = [
    ['image' => 'hero-2.jpg', 'label' => 'Kirg',         'copy' => 'Iga trenn, iga serv, iga punkt.'],
    ['image' => 'hero-4.jpg', 'label' => 'Kogukond',     'copy' => 'Klubi, mis kannab linna nime.'],
    ['image' => 'hero-1.jpg', 'label' => 'Distsipliin',  'copy' => 'Töö, mida ei näe tribüünilt.'],
    ['image' => 'hero-5.jpg', 'label' => 'Võistlustuli', 'copy' => 'Iga mäng loeb, iga hooaeg loeb.'],
    ['image' => 'hero-3.jpg', 'label' => 'Tulevik',      'copy' => 'Noored, kes kannavad särki edasi.'],
  ];
@endphp

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/home-page-test-template.blade.php -->
  @endif

  <style>
    @media (prefers-reduced-motion: reduce) {
      [data-rail-track] { height: auto !important; }
      [data-rail-track] > div { position: static !important; height: auto !important; overflow: visible !important; }
      [data-rail-inner] { overflow-x: auto; padding-block: 1rem; }
    }
  </style>

  {{-- PINNED KINETIC INTRO --}}
  <section class="relative bg-[var(--skduo-hero-bg)] text-paper">
    <div data-editorial-track class="relative h-[240vh]">
      <div class="sticky top-0 h-screen overflow-hidden">
        <div data-editorial-photo class="absolute inset-0 opacity-0" style="transform: scale(1.2)">
          <img src="{{ \Roots\asset('resources/images/hero/hero-2.jpg') }}" alt="" class="h-full w-full object-cover grayscale contrast-110 saturate-75">
          <div class="absolute inset-0 bg-gradient-to-t from-[var(--skduo-hero-bg)] via-[var(--skduo-hero-bg)]/25 to-[var(--skduo-hero-bg)]/70"></div>
        </div>

        <div class="relative z-10 flex h-full flex-col items-center justify-center px-4 text-center">
          <span data-editorial-kicker class="mb-6 inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.24em] text-green-mint">
            <span class="h-px w-8 bg-green-mint"></span>
            SK DUO — Bigbank Tartu
            <span class="h-px w-8 bg-green-mint"></span>
          </span>

          <h1 data-editorial-headline class="font-serif-display text-[clamp(3rem,13vw,10.5rem)] leading-[0.88] tracking-normal will-change-transform">
            Üks klubi.<br>
            <em class="text-green-mint">Üks kirg.</em>
          </h1>
        </div>

        <div data-hero3d-accent-box class="pointer-events-none absolute left-1/2 top-1/2 hidden h-72 w-72 -translate-x-1/2 -translate-y-1/2 md:block lg:h-[30rem] lg:w-[30rem]">
          <div data-hero3d-accent-wrap class="h-full w-full">
            <canvas data-hero3d-accent-canvas class="h-full w-full" aria-hidden="true"></canvas>
          </div>
        </div>

        <a
          href="#editorial-rail"
          data-editorial-cue
          class="skduo-scroll-cue absolute bottom-7 left-1/2 z-20 hidden -translate-x-1/2 lg:inline-flex"
          aria-label="{{ esc_attr__('Scroll to the next section', 'skduo') }}"
        >
          <span class="skduo-scroll-cue__mouse" aria-hidden="true">
            <span class="skduo-scroll-cue__dot"></span>
          </span>
        </a>
      </div>
    </div>
  </section>

  {{-- RALLY 3D — real Three.js scene (same one used on the live front page), scroll-scrubbed --}}
  <section id="rally-3d" class="bg-[var(--skduo-hero-bg)] text-paper">
    <div class="px-4 pb-14 pt-20 sm:px-6 md:px-12 md:pt-28">
      <span class="mb-4 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">
        <span class="h-0.5 w-8 bg-green-mint"></span>
        Mängu rütm
      </span>
      <h2 class="font-serif-display max-w-3xl text-4xl leading-[0.92] tracking-normal sm:text-5xl md:text-7xl">
        Üks serv.<br>Kolm puudet.<br><em class="text-green-mint">Üks punkt.</em>
      </h2>
      <p class="mt-6 max-w-xl text-base leading-relaxed text-paper/72 md:text-lg">
        Skrolli läbi ralli: serv, vastuvõtt, tõste, rünnak ja kaitse liiguvad üle väljaku nagu päris mängus.
      </p>
    </div>

    <div data-rally-track class="relative h-[260vh]">
      <div class="sticky top-0 h-screen overflow-hidden">
        <canvas data-rally-canvas class="absolute inset-0 block h-full w-full" aria-hidden="true"></canvas>

        <div class="pointer-events-none absolute inset-x-0 bottom-[7vh] z-10 hidden items-end justify-between gap-8 px-8 md:flex md:px-12">
          <div>
            <div data-rally-num class="mb-3 text-xs font-bold uppercase tracking-[0.22em] text-green-mint">01 / 05</div>
            <div data-rally-label class="font-serif-display text-5xl leading-none md:text-7xl">Serv</div>
            <div data-rally-desc class="mt-3 max-w-sm text-sm leading-relaxed text-paper/58">Mäng algab survega tagajoone tagant.</div>
          </div>

          <div class="hidden gap-2 md:flex">
            @foreach(range(0, 4) as $i)
              <div data-rally-pip="{{ $i }}" class="h-[3px] w-9 bg-paper/25"></div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- HORIZONTAL SCROLL RAIL --}}
  <section id="editorial-rail" class="bg-[var(--skduo-hero-bg)]">
    <div class="px-4 pb-10 pt-16 text-paper sm:px-6 md:px-12 md:pt-24">
      <span class="mb-4 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">
        <span class="h-0.5 w-8 bg-green-mint"></span>
        Viis sõna, mis meid kirjeldavad
      </span>
      <h2 data-kinetic class="font-serif-display max-w-3xl text-4xl leading-[0.95] tracking-normal sm:text-5xl md:text-6xl">
        Skrolli edasi. Külg edasi.
      </h2>
    </div>

    <div data-rail-track class="relative h-[420vh]">
      <div class="sticky top-0 flex h-screen items-center overflow-hidden">
        <div data-rail-inner class="flex items-stretch gap-6 px-[6vw] will-change-transform">
          @foreach($railCards as $card)
            <article class="relative flex h-[62vh] w-[78vw] shrink-0 flex-col justify-end overflow-hidden rounded-[28px] border border-white/10 sm:w-[46vw] lg:w-[30vw]">
              <img
                src="{{ \Roots\asset('resources/images/hero/' . $card['image']) }}"
                alt=""
                class="absolute inset-0 h-full w-full object-cover grayscale contrast-110 saturate-75"
                loading="lazy"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-[var(--skduo-hero-bg)] via-[var(--skduo-hero-bg)]/10 to-transparent"></div>
              <div class="relative z-10 p-7 text-paper md:p-8">
                <div class="font-serif-display text-4xl leading-none md:text-5xl">{{ $card['label'] }}</div>
                <p class="mt-3 max-w-xs text-sm leading-relaxed text-paper/68">{{ $card['copy'] }}</p>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  {{-- NOTE --}}
  <section class="px-4 py-16 sm:px-6 md:px-12 md:py-24">
    <div class="mx-auto max-w-3xl text-center" data-reveal>
      <span class="mb-4 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
        <span class="h-px w-8 bg-green-deep"></span>
        Test-mall
      </span>
      <h2 class="font-serif-display text-4xl leading-[0.95] tracking-normal sm:text-5xl">
        See on eraldi test-mall, mitte päris avaleht.
      </h2>
      <p class="mt-5 text-base leading-relaxed text-ink/70 md:text-lg">
        Määra see lehele menüüst „Lehe atribuudid → Mall". Kui stseen sobib, saab selle liita päris avalehe kangelaslõiguks.
      </p>
    </div>
  </section>
@endsection
