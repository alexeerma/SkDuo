{{--
  Single player template.
--}}
@extends('layouts.app')

@php
  $player = skduo_player_data(get_post());
  $playerSeasons = ! empty($player->seasons)
    ? implode(', ', array_map(function ($season) {
      return $season['name'] ?? '';
    }, $player->seasons))
    : '';
  $facts = [
    'Number' => $player->number,
    'Positsioon' => $player->position,
    'Pikkus' => $player->height,
    'Kaal' => $player->weight,
    'Rünnakukõrgus' => $player->spike,
    'Blokikõrgus' => $player->block,
    'Sünniaasta' => $player->year,
    'Meeskond' => $player->team,
    'Hooajad' => $playerSeasons,
  ];
@endphp

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/single-mangija.blade.php -->
  @endif

  <article class="bg-paper">
    <header class="relative overflow-hidden border-b border-ink/10 bg-ink text-paper">
      <div class="grid min-h-[auto] grid-cols-1 lg:min-h-[calc(100vh-96px)] lg:grid-cols-[0.95fr_1.05fr]">
        <div class="relative min-h-[320px] overflow-hidden bg-green-deep sm:min-h-[420px]">
          @if($player->image_large)
            <img
              src="{{ esc_url($player->image_large) }}"
              alt="{{ esc_attr($player->name) }}"
              class="absolute inset-0 h-full w-full object-cover"
              fetchpriority="high"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-ink/70 via-ink/5 to-transparent"></div>
          @else
            <div class="grid h-full min-h-[320px] place-items-center bg-hero-dark sm:min-h-[420px]">
              <span class="font-display text-7xl leading-none text-green-mint sm:text-8xl">{{ $player->initials }}</span>
            </div>
          @endif

          @if($player->number)
            <div class="absolute bottom-5 left-5 font-display text-7xl leading-none text-lime sm:bottom-6 sm:left-6 sm:text-8xl md:text-[9rem]">
              #{{ $player->number }}
            </div>
          @endif
        </div>

        <div class="flex flex-col justify-center px-4 py-12 sm:px-6 md:px-12 md:py-20">
          <a href="{{ esc_url(home_url('/meeskonnad')) }}" class="mb-8 inline-flex w-fit items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-green-mint transition hover:text-lime">
            ← Meeskonnad
          </a>

          @if($player->team || $player->position)
            <div class="mb-5 flex flex-wrap gap-2">
              @if($player->team)
                <span class="bg-lime px-3 py-1.5 text-[0.62rem] font-extrabold uppercase tracking-widest text-ink">{{ $player->team }}</span>
              @endif
              @if($player->position)
                <span class="border border-paper/25 px-3 py-1.5 text-[0.62rem] font-extrabold uppercase tracking-widest text-paper/70">{{ $player->position }}</span>
              @endif
            </div>
          @endif

          <h1 class="font-display text-5xl leading-[0.88] tracking-wide sm:text-6xl md:text-8xl">
            {{ $player->name }}
          </h1>

          @if(trim(wp_strip_all_tags($player->description)))
            <div class="mt-8 max-w-2xl text-base leading-relaxed text-paper/68 md:text-lg">
              {!! $player->description !!}
            </div>
          @endif

          @if($player->profile_url || $player->stats_url)
            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
              @if($player->profile_url)
                <a
                  href="{{ esc_url($player->profile_url) }}"
                  target="_blank"
                  rel="noopener"
                  class="inline-flex w-full items-center justify-center gap-2 bg-lime px-7 py-4 text-xs font-extrabold uppercase tracking-widest text-ink transition hover:-translate-y-0.5 hover:bg-paper sm:w-fit"
                  data-magnet="0.3"
                >
                  Profiil →
                </a>
              @endif

              @if($player->stats_url)
                <a
                  href="{{ esc_url($player->stats_url) }}"
                  target="_blank"
                  rel="noopener"
                  class="inline-flex w-full items-center justify-center gap-2 border border-paper/25 px-7 py-4 text-xs font-extrabold uppercase tracking-widest text-paper transition hover:-translate-y-0.5 hover:border-lime hover:text-lime sm:w-fit"
                  data-magnet="0.3"
                >
                  Dataweb statistika →
                </a>
              @endif
            </div>
          @endif
        </div>
      </div>
    </header>

    <section class="px-4 py-12 sm:px-6 md:px-12 md:py-16">
      <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex items-end justify-between gap-6 border-b border-ink/10 pb-5">
          <div>
            <span class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-green-deep">
              <span class="h-0.5 w-8 bg-green-deep"></span>
              Mängija andmed
            </span>
            <h2 class="font-display text-4xl leading-none tracking-wide sm:text-5xl">Profiil</h2>
          </div>
        </div>

        <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
          @foreach($facts as $label => $value)
            <div class="border border-ink/10 bg-cream p-5">
              <dt class="text-[0.62rem] font-extrabold uppercase tracking-widest text-ink/45">{{ $label }}</dt>
              <dd class="mt-3 font-display text-3xl leading-none text-ink sm:text-4xl">{{ $value ?: '-' }}</dd>
            </div>
          @endforeach
        </dl>
      </div>
    </section>
  </article>
@endsection
