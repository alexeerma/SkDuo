{{--
  Homepage (front-page.blade.php).
  $nextMatch and $upcomingMatches are injected by App\View\Composers\NextMatch.
--}}
@extends('layouts.app')

@section('content')
  {{-- HERO --}}
  @php
    $hero = skduo_get_home_hero_data(get_queried_object_id());
    $heroImages = collect($hero->images);
    $blogPosts = skduo_get_latest_posts(6);
    $postsPageId = (int) get_option('page_for_posts');
    $postsPageUrl = $postsPageId ? get_permalink($postsPageId) : home_url('/uudised');

    // "Tule trenni" opens the same training sign-up popup as /noored — same options, same form.
    $noored = function_exists('skduo_get_noored_options') ? skduo_get_noored_options() : [];
    $trainingFormEmbed = trim((string) ($noored['form_iframe_embed'] ?? ''));

    $courtSponsorLogos = function_exists('skduo_get_court_sponsor_logos') ? skduo_get_court_sponsor_logos() : (object) ['bigbank' => '', 'city' => ''];
  @endphp

  <section data-hero-spotlight data-hero-intro class="relative min-h-[calc(100svh-122px)] overflow-hidden bg-[var(--skduo-hero-bg)] text-paper md:min-h-[calc(100vh-96px)]">
    <div class="skduo-hero-mosaic absolute inset-0 z-0 opacity-70">
      <div class="grid h-full w-full grid-cols-2 md:grid-cols-3">
        @foreach($heroImages as $image)
          <div class="relative overflow-hidden">
            <img src="{{ esc_url($image) }}" alt="" class="h-full w-full object-cover grayscale contrast-110 saturate-75" loading="{{ $loop->first ? 'eager' : 'lazy' }}" @if($loop->first) fetchpriority="high" @endif>
          </div>
        @endforeach
      </div>
    </div>

    <div class="absolute inset-0 z-[1] bg-[var(--skduo-hero-bg)]/82"></div>
    <div class="absolute inset-x-0 bottom-0 z-[2] h-1/2 bg-gradient-to-t from-[var(--skduo-hero-bg)] to-transparent"></div>

    <div class="relative z-10 flex min-h-[calc(100svh-122px)] flex-col px-4 py-8 sm:px-6 md:min-h-[calc(100vh-96px)] md:px-12 md:py-10">
      <div class="skduo-hero-kicker grid grid-cols-1 gap-3 pt-3 text-[0.64rem] font-bold uppercase tracking-[0.16em] text-paper/55 sm:grid-cols-3 sm:text-[0.68rem]">
        <div>{{ $hero->location }}</div>
        <div class="flex items-center gap-2 md:justify-center">
          <span class="h-2 w-2 rounded-full bg-green-mint animate-pulse"></span>
          {{ $hero->season }}
        </div>
        <div class="md:text-right">{{ $hero->established }}</div>
      </div>

      <div class="skduo-hero-layout mt-auto grid gap-8 pb-6 pt-12 lg:grid-cols-[minmax(0,1fr)_minmax(480px,560px)] lg:items-end xl:grid-cols-[minmax(0,1fr)_minmax(520px,600px)]">
        <div>
          <h1 class="skduo-hero-title font-serif-display max-w-5xl text-[clamp(3rem,16vw,7.2rem)] leading-[1.1] tracking-normal">
            <span class="skduo-hero-title__line">{{ $hero->title_line_1 }}</span>
            <span class="skduo-hero-title__line">{{ $hero->title_line_2 }} <em class="text-green-mint">{{ $hero->title_emphasis }}</em><span class="text-paper/30" aria-hidden="true">—</span></span>
          </h1>

          <div class="skduo-hero-copy mt-6">
            <p class="max-w-xl text-sm leading-relaxed text-paper/78 md:text-base lg:text-lg">
              {{ $hero->subtitle }}
            </p>

            <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
              <a href="{{ esc_url($hero->primary_url) }}" class="inline-flex items-center justify-center gap-3 rounded-full bg-green-mint px-6 py-3.5 text-xs font-extrabold uppercase tracking-widest text-ink shadow-cta-glow transition hover:-translate-y-0.5 hover:bg-lime focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-paper md:text-sm" data-magnet="0.3">
                {{ $hero->primary_label }}
                <span aria-hidden="true">→</span>
              </a>
              @if($trainingFormEmbed !== '')
                <button
                  type="button"
                  class="inline-flex items-center justify-center rounded-full border border-paper/35 px-6 py-3.5 text-xs font-extrabold uppercase tracking-widest text-paper transition hover:border-paper hover:bg-paper hover:text-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-mint md:text-sm"
                  data-training-modal-open
                  data-magnet="0.3"
                >
                  {{ $hero->secondary_label }}
                </button>
              @else
                <a href="{{ esc_url($hero->secondary_url) }}" class="inline-flex items-center justify-center rounded-full border border-paper/35 px-6 py-3.5 text-xs font-extrabold uppercase tracking-widest text-paper transition hover:border-paper hover:bg-paper hover:text-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-mint md:text-sm" data-magnet="0.3">
                  {{ $hero->secondary_label }}
                </a>
              @endif
            </div>
          </div>
        </div>

        <div
          role="group"
          aria-label="Järgmine mäng"
          class="skduo-hero-match-card border border-white/10 bg-white/8 p-4 backdrop-blur-md sm:p-5 xl:p-6"
          @if($nextMatch) data-countdown-target="{{ $nextMatch->date_ts }}" @endif
        >
          <div class="mb-4 flex items-center justify-between gap-3">
            <span class="text-[0.65rem] font-extrabold uppercase tracking-[0.18em] text-green-mint">Järgmine mäng</span>
            <span class="h-2 w-2 rounded-full bg-green-mint animate-pulse" aria-hidden="true"></span>
          </div>

          @if($nextMatch)
            {{-- Decorative live countdown; the <time> element below carries the same date/time accessibly. --}}
            <div class="mb-4 grid grid-cols-4 gap-1.5 sm:gap-2" aria-hidden="true">
              @foreach(['d' => 'päeva', 'h' => 'tundi', 'm' => 'min', 's' => 'sek'] as $key => $label)
                <div class="rounded border border-white/10 bg-white/5 px-1 py-2 text-center">
                  <div class="font-display text-lg leading-none text-paper sm:text-xl" data-cd="{{ $key }}">--</div>
                  <div class="mt-1 text-[0.5rem] font-bold uppercase tracking-widest text-paper/60">{{ $label }}</div>
                </div>
              @endforeach
            </div>

            <div class="border-y border-white/10 py-5">
              <div class="skduo-hero-match-teams flex items-center justify-between gap-3 sm:gap-4">
                <div class="min-w-0 flex-1">
                  <div class="skduo-hero-match-team-name font-display leading-none">{{ $nextMatch->home }}</div>
                  <div class="mt-1 text-[0.6rem] font-bold uppercase tracking-widest text-paper/60">Kodu</div>
                </div>
                <div class="skduo-hero-match-vs font-display text-green-mint">VS</div>
                <div class="min-w-0 flex-1 text-right">
                  <div class="skduo-hero-match-team-name font-display leading-none">{{ $nextMatch->away }}</div>
                  <div class="mt-1 text-[0.6rem] font-bold uppercase tracking-widest text-paper/60">Külaline</div>
                </div>
              </div>
            </div>

            <div class="mt-4 flex flex-wrap justify-between gap-3 text-xs font-semibold text-paper/70">
              <span>{{ $nextMatch->venue }}</span>
              <time class="shrink-0" datetime="{{ $nextMatch->date }}">{{ wp_date('d.m · H:i', $nextMatch->date_ts) }}</time>
            </div>

            <div class="mt-4 flex gap-2">
              <a
                href="{{ esc_url($nextMatch->permalink) }}"
                class="block flex-1 rounded-full border border-white/20 px-4 py-3 text-center text-xs font-extrabold uppercase tracking-widest text-paper transition hover:border-green-mint hover:text-green-mint focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-paper"
              >
                Info<span class="sr-only">: {{ $nextMatch->home }} – {{ $nextMatch->away }}</span>
              </a>
              @if(! empty($nextMatch->free_entry))
                <span class="block flex-1 rounded-full bg-lime px-4 py-3 text-center text-xs font-extrabold uppercase tracking-widest text-ink">
                  Tasuta sissepääs
                </span>
              @elseif($nextMatch->is_home && $nextMatch->ticket_url)
                <a
                  href="{{ esc_url($nextMatch->ticket_url) }}"
                  class="block flex-1 rounded-full bg-green-mint px-4 py-3 text-center text-xs font-extrabold uppercase tracking-widest text-ink transition hover:-translate-y-0.5 hover:bg-lime focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-paper"
                  target="_blank" rel="noopener noreferrer"
                  data-magnet="0.3"
                >
                  Osta pilet →
                </a>
              @endif
            </div>
          @else
            <p class="border-y border-white/10 py-5 text-sm text-paper/65">Järgmine mäng peagi teada.</p>
          @endif
        </div>
      </div>

      <a href="#court" class="skduo-scroll-cue absolute bottom-7 left-1/2 z-20 hidden -translate-x-1/2 lg:inline-flex" aria-label="{{ esc_attr__('Scroll to the next section', 'skduo') }}">
        <span class="skduo-scroll-cue__mouse" aria-hidden="true">
          <span class="skduo-scroll-cue__dot"></span>
        </span>
      </a>
    </div>
  </section>

  <x-training-modal :form-embed="$trainingFormEmbed" :title="$noored['form_popup_title'] ?? null" />

  {{-- RALLY 3D --}}
  <section id="court" class="bg-[var(--skduo-hero-bg)] text-paper">
    <div class="px-4 pb-14 pt-20 sm:px-6 md:px-12 md:pt-32">
      <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.95fr_0.8fr] lg:items-start">
        <div data-reveal>
          <span class="mb-4 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">
            <span class="h-0.5 w-8 bg-green-mint"></span>
            Mängu rütm
          </span>
          <h2 class="font-serif-display text-4xl leading-[0.92] tracking-normal sm:text-5xl md:text-7xl lg:text-8xl">
            Üks serv.<br>Kolm puudet.<br><em class="text-green-mint">Üks punkt.</em>
          </h2>
        </div>

        <div class="lg:pt-9" data-reveal data-reveal-delay="120">
          <p class="max-w-xl text-base leading-relaxed text-paper/72 md:text-lg">
            Skrolli läbi ralli: serv, vastuvõtt, tõste, rünnak ja kaitse liiguvad üle väljaku nagu päris mängus.
          </p>
          <p class="mt-6 text-xs font-bold uppercase tracking-[0.18em] text-paper/60">Scroll — jälgi palli trajektoori ↓</p>
        </div>
      </div>
    </div>

    <div
      data-rally-track
      data-rally-sponsor-logo="{{ $courtSponsorLogos->bigbank }}"
      @if($courtSponsorLogos->city) data-rally-city-logo="{{ $courtSponsorLogos->city }}" @endif
      class="relative h-[260vh]"
    >
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

  {{-- BLOG BENTO --}}
  <section class="relative overflow-hidden bg-[var(--skduo-section-dark)] px-4 py-16 text-paper sm:px-6 md:px-10 md:py-28">
    <div class="relative z-10 mx-auto max-w-[1536px]">
      <div class="mb-10 flex flex-wrap items-end justify-between gap-8">
        <div data-reveal class="skduo-news-heading">
          <span class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">
            <span class="h-0.5 w-8 bg-green-mint"></span>
            Uudised
          </span>
          <h2 class="font-serif-display max-w-3xl text-4xl leading-[0.95] tracking-normal md:text-6xl">
            Klubi elu,<br><em class="text-green-mint">väljakult ja saalist.</em>
          </h2>
        </div>

        <a href="{{ esc_url($postsPageUrl) }}" class="inline-flex items-center gap-3 border-b border-green-mint pb-1 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint transition hover:text-lime">
          Kõik uudised
          <span aria-hidden="true">→</span>
        </a>
      </div>

      @if(! empty($blogPosts))
        <div data-bento-grid class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4 lg:auto-rows-[148px]">
          @foreach($blogPosts as $post)
            @php
              $layout = match($loop->index) {
                2 => 'md:col-span-2 lg:col-span-2 lg:row-span-3',
                3 => 'md:col-span-2 lg:col-span-2 lg:row-span-3',
                default => 'lg:row-span-2',
              };
              $isWide = in_array($loop->index, [2, 3], true);
              $cardPadding = $isWide ? 'lg:p-12' : 'lg:p-8';
              $titleSize = $isWide ? 'text-3xl md:text-4xl lg:text-5xl' : 'text-2xl md:text-3xl lg:text-4xl';
              $excerptSize = $isWide ? 'line-clamp-3 text-base md:text-lg' : 'line-clamp-2 text-sm md:text-base';
            @endphp

            <a
              href="{{ esc_url($post->permalink) }}"
              data-reveal
              data-bento-card
              class="skduo-bento-card skduo-news-card group {{ $layout }} {{ $cardPadding }} relative flex min-h-[300px] overflow-hidden rounded-[28px] border border-white/12 bg-[var(--skduo-card-dark)] p-7 text-paper transition duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-mint md:min-h-[360px] md:p-10 lg:min-h-0"
              style="--glow-color: 111, 200, 181; --reveal-delay: {{ $loop->index * 90 }}ms;"
            >
              @if($post->image)
                <img src="{{ esc_url($post->image) }}" alt="" class="skduo-news-card__image absolute inset-0 h-full w-full object-cover opacity-22 grayscale saturate-75 transition duration-500 group-hover:scale-105 group-hover:opacity-30" loading="lazy">
                <span class="absolute inset-0 bg-gradient-to-b from-[var(--skduo-section-dark)]/78 via-[var(--skduo-section-dark)]/62 to-[var(--skduo-section-dark)]/92"></span>
              @else
                <span class="absolute inset-0 bg-card-fallback" style="--fallback-pos: 25% 10%; --fallback-opacity: 20%"></span>
              @endif

              <span class="skduo-news-card__beam" aria-hidden="true"></span>

              <span class="skduo-bento-volleyball" aria-hidden="true">
                <canvas data-bento-volleyball-canvas></canvas>
              </span>

              <span class="relative z-10 flex min-h-full w-full flex-col justify-between gap-7">
                <span class="flex items-start justify-between gap-4">
                  <span class="text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">{{ $post->category }}</span>
                  <time datetime="{{ esc_attr($post->date) }}" class="shrink-0 text-xs font-bold uppercase tracking-[0.16em] text-paper/52">{{ $post->date_label }}</time>
                </span>

                <span>
                  <h3 class="mb-3 font-serif-display {{ $titleSize }} leading-none tracking-normal">
                    {{ $post->title }}
                  </h3>
                  <p class="{{ $excerptSize }} max-w-3xl leading-relaxed text-paper/68">
                    {{ $post->excerpt }}
                  </p>
                </span>
              </span>
            </a>
          @endforeach
        </div>
      @else
        <div class="border border-white/10 bg-white/5 p-8 text-paper/68">
          Uudiseid pole veel avaldatud.
        </div>
      @endif
    </div>
  </section>

  {{-- CALENDAR PREVIEW --}}
  <section class="px-4 py-14 sm:px-6 md:px-12 md:py-20">
    <div class="flex items-end justify-between mb-10 gap-8 flex-wrap">
      <div>
        <span class="inline-flex items-center gap-2 text-green-deep text-xs font-extrabold uppercase tracking-widest mb-2">
          <span class="w-7 h-0.5 bg-green-deep"></span>
          Kalender
        </span>
        <h2 class="font-display text-4xl tracking-wide sm:text-5xl">JÄRGMISED <span class="bg-bigbank-gradient bg-clip-text text-transparent">MÄNGUD</span></h2>
      </div>
      <a href="/kalender" class="text-xs font-extrabold uppercase tracking-widest text-ink border-b border-ink pb-0.5 hover:gap-3 transition-all">Kogu hooaeg →</a>
    </div>

    @if(count($upcomingMatches))
      <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($upcomingMatches as $match)
          <x-game-card :match="$match" data-reveal />
        @endforeach
      </div>
    @else
      <div class="border border-ink/10 bg-black/5 p-8 text-ink/68">
        Järgmised mängud on selgumisel.
      </div>
    @endif
  </section>

@endsection
