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

  $months = [];
  foreach ($matches as $match) {
    $timestamp = (int) $match->date_ts;
    if (! $timestamp) {
      continue;
    }

    $monthKey = wp_date('Y-m', $timestamp);
    if (! isset($months[$monthKey])) {
      $months[$monthKey] = [
        'label' => wp_date('F Y', $timestamp),
        'items' => [],
      ];
    }

    $months[$monthKey]['items'][] = $match;
  }
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

    @if(! empty($months))
      <div class="space-y-14">
        @foreach($months as $month)
          <section class="grid gap-5 border-t border-ink/10 pt-6 lg:grid-cols-[260px_minmax(0,1fr)]">
            <header class="lg:sticky lg:top-28 lg:self-start">
              <h2 class="font-display text-3xl leading-none tracking-wide text-green-deep sm:text-4xl md:text-5xl">
                {{ $month['label'] }}
              </h2>
              <div class="mt-2 text-xs font-extrabold uppercase tracking-[0.18em] text-ink/62">
                {{ count($month['items']) }} {{ count($month['items']) === 1 ? 'mäng' : 'mängu' }}
              </div>
            </header>

            <div class="space-y-3">
              @foreach($month['items'] as $match)
                @php
                  $isPast = $match->date_ts && $match->date_ts < time();
                  $isHome = (bool) $match->is_home;
                  $statusLabel = match($match->status) {
                    'live' => 'Live',
                    'finished' => 'Lõppenud',
                    default => $isPast ? 'Mängitud' : 'Tulekul',
                  };
                @endphp

                <article class="group grid gap-4 border border-ink/10 bg-white p-4 transition hover:border-green-deep/45 hover:shadow-[0_24px_60px_rgba(10,21,24,.08)] md:grid-cols-[120px_minmax(0,1fr)_auto] md:items-center md:p-5">
                  <div class="flex items-center gap-4 md:block">
                    <time datetime="{{ esc_attr($match->date) }}" class="block">
                      <span class="block font-display text-4xl leading-none text-ink md:text-5xl">{{ wp_date('d', $match->date_ts) }}</span>
                      <span class="mt-1 block text-[0.68rem] font-extrabold uppercase tracking-[0.18em] text-ink/62">{{ wp_date('D · H:i', $match->date_ts) }}</span>
                    </time>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-[0.6rem] font-extrabold uppercase tracking-widest md:mt-4 {{ $isPast ? 'bg-ink/8 text-ink/55' : 'bg-green-deep text-paper' }}">
                      {{ $statusLabel }}
                    </span>
                  </div>

                  <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                      <span class="font-display text-2xl leading-none tracking-wide {{ $isHome ? 'text-green-deep' : 'text-ink' }} sm:text-3xl">{{ $match->home }}</span>
                      <span class="text-xs font-extrabold uppercase tracking-widest text-ink/62">vs</span>
                      <span class="font-display text-2xl leading-none tracking-wide {{ $isHome ? 'text-ink' : 'text-green-deep' }} sm:text-3xl">{{ $match->away }}</span>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-sm font-semibold text-ink/62">
                      @if($match->venue)
                        <span>{{ $match->venue }}</span>
                      @endif
                      @if($match->league)
                        <span>{{ $match->league }}</span>
                      @endif
                      <span>{{ $isHome ? 'Kodumäng' : 'Võõrsilmäng' }}</span>
                    </div>
                  </div>

                  <div class="flex flex-wrap items-center gap-3 md:justify-end">
                    @if($match->home_score || $match->away_score || $match->status === 'finished')
                      <div class="font-display text-3xl leading-none text-ink">
                        {{ $match->home_score }}:{{ $match->away_score }}
                      </div>
                    @endif

                    <a href="{{ esc_url($match->permalink) }}" class="inline-flex items-center rounded-full border border-ink/15 px-4 py-2 text-xs font-extrabold uppercase tracking-widest text-ink transition hover:border-green-deep hover:text-green-deep">
                      Info
                    </a>
                    @if(($isPast || $match->status === 'finished') && ! empty($match->dataproject_url))
                      <a href="{!! esc_url($match->dataproject_url) !!}" class="inline-flex items-center rounded-full border border-ink/15 px-4 py-2 text-xs font-extrabold uppercase tracking-widest text-ink transition hover:border-green-deep hover:text-green-deep" target="_blank" rel="noopener noreferrer">
                        Statistika
                      </a>
                    @endif
                    @if(! $isPast && ! empty($match->free_entry))
                      <span class="inline-flex items-center rounded-full bg-lime px-4 py-2 text-xs font-extrabold uppercase tracking-widest text-ink">
                        Tasuta sissepääs
                      </span>
                    @elseif($isHome && $match->ticket_url && ! $isPast)
                      <a href="{{ esc_url($match->ticket_url) }}" class="inline-flex items-center rounded-full bg-green-deep px-4 py-2 text-xs font-extrabold uppercase tracking-widest text-paper transition hover:bg-ink" target="_blank" rel="noopener noreferrer">
                        Piletid
                      </a>
                    @endif
                  </div>
                </article>
              @endforeach
            </div>
          </section>
        @endforeach
      </div>
    @else
      <div class="border border-ink/10 bg-white p-8 text-ink/62">
        Mänge pole veel lisatud.
      </div>
    @endif
  </div>
</section>
