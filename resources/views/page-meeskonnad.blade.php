{{--
  Teams page.
--}}
@extends('layouts.app')

@php
  $teams = skduo_get_teams_with_players();
  // Hide teams without any players (across all seasons) on the frontend.
  $visibleTeams = array_values(array_filter($teams, fn ($team) => ! empty($team->players)));
  $meeskonnadHeroImage = function_exists('skduo_get_meeskonnad_hero_image') ? skduo_get_meeskonnad_hero_image() : '';
  $accentClasses = [
    'green-deep' => 'bg-green-deep text-paper',
    'green-mid' => 'bg-green-mid text-paper',
    'lime' => 'bg-lime text-ink',
  ];
@endphp

@section('content')
  <section class="bg-paper border-b border-ink/10">
    <div class="grid min-h-[auto] grid-cols-1 lg:min-h-[520px] lg:grid-cols-[1.15fr_0.85fr]">
      <div class="flex flex-col justify-center px-4 py-14 sm:px-6 md:px-12 md:py-20">
        <span class="inline-flex items-center gap-2 text-green-deep text-xs font-extrabold uppercase tracking-widest mb-5">
          <span class="w-8 h-0.5 bg-green-deep"></span>
          Võistkonnad
        </span>

        <h1 class="max-w-4xl font-display text-5xl leading-[0.88] tracking-wide sm:text-6xl md:text-8xl">
          TARTU PLATSIL ON RUUMI IGALE ROLLILE.
        </h1>

        <p class="mt-7 max-w-2xl text-base md:text-lg leading-relaxed text-ink/65">
          SK DUO võistkonnad ühendavad esindustaseme, ülikoolivõrkpalli ja noortesüsteemi üheks selgeks arenguteeks.
        </p>
      </div>

      @if($meeskonnadHeroImage)
        <div class="relative min-h-[280px] overflow-hidden bg-hero-dark sm:min-h-[360px] lg:min-h-0">
          <img src="{{ esc_url($meeskonnadHeroImage) }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="eager" fetchpriority="high" decoding="async">
          <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-green-deep/45 to-transparent"></div>
        </div>
      @else
        <div class="relative hidden overflow-hidden bg-hero-dark lg:block" aria-hidden="true">
          <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-green-mint/10 animate-float"></div>
        </div>
      @endif
    </div>
  </section>

  <section class="px-4 py-12 sm:px-6 md:px-12 md:py-20">
    @if(empty($teams))
      <div class="border border-ink/10 bg-cream px-6 py-10 md:px-10">
        <div class="max-w-3xl">
          <span class="inline-flex items-center gap-2 text-green-deep text-xs font-extrabold uppercase tracking-widest mb-3">
            <span class="w-7 h-0.5 bg-green-deep"></span>
            Sisu puudub
          </span>
          <h2 class="mb-4 font-display text-4xl leading-none tracking-wide sm:text-5xl">Lisa esimene meeskond wp-administ.</h2>
          <p class="text-sm leading-relaxed text-ink/65">
            Loo esmalt kirje menüüst Meeskonnad. Seejärel lisa mängijad menüüst Mängijad ja vali iga mängija juures õige meeskond.
          </p>
        </div>
      </div>
    @else
      <div class="space-y-16">
      @foreach($visibleTeams as $team)
        <section data-reveal data-team-roster>
          <div class="mb-6 grid gap-5 border-t border-ink/10 pt-7 lg:grid-cols-[0.75fr_1.25fr] lg:items-end">
            <div>
              <div class="mb-3 flex flex-wrap items-center gap-2">
                @if($team->level)
                  <span class="skduo-team-pill border-transparent {{ $accentClasses[$team->accent] ?? $accentClasses['green-deep'] }}">
                    {{ $team->level }}
                  </span>
                @endif
                @if($team->league)
                  <span class="skduo-team-pill border-ink/15 text-ink/60">
                    {{ $team->league }}
                  </span>
                @endif
                @if(! empty($team->seasons))
                  @php
                    // $team->seasons is sorted latest-first — default the picker to it instead of "Kõik hooajad".
                    $defaultSeason = $team->seasons[0];
                  @endphp
                  <div class="skduo-season-picker relative" data-season-picker>
                    <button
                      type="button"
                      class="skduo-team-pill skduo-season-picker__trigger"
                      aria-expanded="false"
                      data-season-trigger
                    >
                      <span class="sr-only">{{ $team->name }}, hooaeg: </span>
                      <span data-season-label>{{ $defaultSeason['name'] }}</span>
                      <span class="skduo-season-picker__icon" aria-hidden="true"></span>
                    </button>
                    <div class="skduo-season-picker__menu" data-season-menu>
                      <button type="button" class="skduo-season-picker__option" data-season-option data-season-value="all">
                        Kõik hooajad
                      </button>
                      @foreach($team->seasons as $season)
                        <button type="button" class="skduo-season-picker__option{{ $season['slug'] === $defaultSeason['slug'] ? ' is-active' : '' }}" data-season-option data-season-value="{{ esc_attr($season['slug']) }}">
                          {{ $season['name'] }}
                        </button>
                      @endforeach
                    </div>
                  </div>
                @endif
              </div>
              <h2 class="font-display text-4xl leading-none tracking-wide sm:text-5xl md:text-6xl">{{ $team->name }}</h2>
            </div>

            @if($team->summary)
              <div class="lg:max-w-3xl">
                <p class="text-sm md:text-base leading-relaxed text-ink/65">{{ $team->summary }}</p>
              </div>
            @endif
          </div>

          <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($team->players as $player)
              <x-player-card :player="$player" />
            @endforeach
          </div>

          <div class="skduo-season-empty mt-4 hidden border border-dashed border-ink/20 p-6 text-sm font-bold uppercase tracking-widest text-ink/62">
            Selle hooaja mängijaid pole veel lisatud.
          </div>

          @if(! empty($team->staff))
            <div class="mt-10">
              <div class="mb-4 flex items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-ink/62">
                <span class="h-0.5 w-6 bg-ink/20"></span>
                Treenerid ja personal
              </div>
              <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach($team->staff as $member)
                  <x-player-card :player="$member" :show-stats="false" />
                @endforeach
              </div>
            </div>
          @endif
        </section>
      @endforeach
      </div>
    @endif
  </section>

  <script>
    (function () {
      document.querySelectorAll('[data-team-roster]').forEach(function (section) {
        var picker = section.querySelector('[data-season-picker]');
        var options = Array.prototype.slice.call(section.querySelectorAll('[data-season-option]'));
        var cards = Array.prototype.slice.call(section.querySelectorAll('[data-player-card]'));
        var empty = section.querySelector('.skduo-season-empty');

        if (!picker || !options.length || !cards.length) {
          return;
        }

        // Defaults to whichever option the template marked .is-active (the latest season), falling back to "all".
        var initialOption = options.filter(function (option) {
          return option.classList.contains('is-active');
        })[0];
        var selectedSeason = initialOption ? (initialOption.getAttribute('data-season-value') || 'all') : 'all';

        function updateRoster() {
          var visibleCount = 0;

          cards.forEach(function (card) {
            var seasons = (card.getAttribute('data-seasons') || '').split(/\s+/).filter(Boolean);
            var isVisible = selectedSeason === 'all' || seasons.indexOf(selectedSeason) !== -1;
            var image = card.querySelector('[data-player-image]');

            card.classList.toggle('hidden', !isVisible);
            if (image) {
              var seasonImages = {};
              try {
                seasonImages = JSON.parse(card.getAttribute('data-season-images') || '{}');
              } catch (error) {
                seasonImages = {};
              }
              var directSeasonImage = selectedSeason !== 'all'
                ? card.getAttribute('data-season-image-' + selectedSeason)
                : '';
              var nextImage = directSeasonImage || seasonImages[selectedSeason] || card.getAttribute('data-default-image') || image.src;

              if (image.src !== nextImage) {
                image.src = nextImage;
              }
            }
            if (isVisible) {
              visibleCount += 1;
            }
          });

          if (empty) {
            empty.classList.toggle('hidden', visibleCount !== 0);
          }
        }

        // Open/close + keyboard handling live in resources/js/seasonPicker.js.
        picker.addEventListener('skduo:season-change', function (event) {
          selectedSeason = event.detail.value || 'all';
          updateRoster();
        });

        updateRoster();
      });
    })();
  </script>
@endsection
