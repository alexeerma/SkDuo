@php
  $matchPosts = get_posts([
    'post_type'      => 'mang',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_key'       => '_match_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
  ]);

  $matches = array_values(array_filter(array_map(
    function ($post) {
      return function_exists('skduo_match_data') ? skduo_match_data($post) : null;
    },
    $matchPosts
  )));

  // A match counts as played once finished, or 3h after start unless marked live.
  $playedCutoff = time() - 3 * HOUR_IN_SECONDS;
  $upcomingMatches = [];
  $playedMatches = [];
  foreach ($matches as $match) {
    if (! (int) $match->date_ts) {
      continue;
    }

    $isPlayed = $match->status === 'finished'
      || ($match->status !== 'live' && (int) $match->date_ts < $playedCutoff);

    if ($isPlayed) {
      $playedMatches[] = $match;
    } else {
      $upcomingMatches[] = $match;
    }
  }

  $groupByMonth = function (array $items) {
    $months = [];
    foreach ($items as $match) {
      $timestamp = (int) $match->date_ts;
      $monthKey = wp_date('Y-m', $timestamp);
      if (! isset($months[$monthKey])) {
        $months[$monthKey] = [
          'label' => wp_date('F Y', $timestamp),
          'items' => [],
        ];
      }
      $months[$monthKey]['items'][] = $match;
    }

    return $months;
  };

  $upcomingMonths = $groupByMonth($upcomingMatches);
  // Newest played match first, so recent results and stats are on top.
  $playedMonths = $groupByMonth(array_reverse($playedMatches));
  $defaultTab = empty($upcomingMatches) && ! empty($playedMatches) ? 'mangitud' : 'tulevased';
  $tabs = [
    'tulevased' => ['label' => 'Tulevased', 'count' => count($upcomingMatches), 'months' => $upcomingMonths, 'empty' => 'Tulevasi mänge pole veel lisatud.'],
    'mangitud'  => ['label' => 'Mängitud', 'count' => count($playedMatches), 'months' => $playedMonths, 'empty' => 'Mängitud mänge veel pole.'],
  ];
@endphp

<section class="bg-paper px-4 py-14 text-ink sm:px-6 md:px-10 md:py-24">
  <div class="mx-auto max-w-[1360px]">
    <div class="mb-12 flex flex-wrap items-end justify-between gap-8">
      <div>
        <span class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
          <span class="h-0.5 w-8 bg-green-deep"></span>
          Kalender
        </span>
        <h1 class="font-serif-display max-w-4xl text-4xl leading-[0.95] tracking-normal sm:text-5xl md:text-7xl">
          Mängud<br><em class="text-green-deep">kuu kaupa.</em>
        </h1>
      </div>

      <div class="max-w-sm text-sm leading-relaxed text-ink/62">
        Kõik sisestatud mängud kuvatakse kronoloogiliselt ning jagatakse automaatselt kuude kaupa.
      </div>
    </div>

    @if(! empty($matches))
      <div data-calendar-tabs data-default-tab="{{ $defaultTab }}">
        {{-- Tab bar is revealed by JS; without JS both lists render in sequence. --}}
        <div role="tablist" aria-label="Mängude vaade" class="mb-12 flex flex-wrap gap-2 border-b border-ink/10 pb-6" hidden data-calendar-tablist>
          @foreach($tabs as $key => $tab)
            <button
              type="button"
              role="tab"
              id="kalender-tab-{{ $key }}"
              aria-controls="kalender-panel-{{ $key }}"
              aria-selected="{{ $key === $defaultTab ? 'true' : 'false' }}"
              tabindex="{{ $key === $defaultTab ? '0' : '-1' }}"
              data-calendar-tab="{{ $key }}"
              class="inline-flex items-center gap-2.5 rounded-full border border-ink/15 px-5 py-2.5 text-xs font-extrabold uppercase tracking-[0.16em] text-ink/62 transition hover:border-green-deep hover:text-green-deep focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-deep aria-selected:border-green-deep aria-selected:bg-green-deep aria-selected:text-paper"
            >
              {{ $tab['label'] }}
              <span class="font-display text-base leading-none tracking-wide opacity-70">{{ $tab['count'] }}</span>
            </button>
          @endforeach
        </div>

        @foreach($tabs as $key => $tab)
          <div role="tabpanel" id="kalender-panel-{{ $key }}" aria-labelledby="kalender-tab-{{ $key }}" data-calendar-panel="{{ $key }}" class="mb-14 last:mb-0">
            @if(! empty($tab['months']))
              <div class="space-y-14">
                @foreach($tab['months'] as $month)
                  @include('partials.calendar-month', ['month' => $month])
                @endforeach
              </div>
            @else
              <div class="border border-ink/10 bg-white p-8 text-ink/62">
                {{ $tab['empty'] }}
              </div>
            @endif
          </div>
        @endforeach
      </div>
    @else
      <div class="border border-ink/10 bg-white p-8 text-ink/62">
        Mänge pole veel lisatud.
      </div>
    @endif
  </div>
</section>
