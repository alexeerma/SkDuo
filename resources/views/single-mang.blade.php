{{--
  Single match template (mang CPT).
--}}
@extends('layouts.app')

@php
  $match = skduo_match_data(get_post());
  $isPast = $match->date_ts && $match->date_ts < time();
  $isHome = (bool) $match->is_home;
  $hasScore = $match->home_score || $match->away_score || $match->status === 'finished';
  $isUpcoming = ! $isPast && $match->status !== 'finished' && $match->date_ts;
  $isFree = ! empty($match->free_entry);
  $hasTicket = $isHome && $match->ticket_url && $isUpcoming && ! $isFree;
  $statusLabel = match ($match->status) {
    'live' => 'Live',
    'finished' => 'Lõppenud',
    default => $isPast ? 'Mängitud' : 'Tulekul',
  };
  $facts = array_filter([
    'Kuupäev' => $match->date_ts ? wp_date('d.m.Y', $match->date_ts) : '',
    'Algus' => $match->date_ts ? wp_date('H:i', $match->date_ts) : '',
    'Mängu koht' => $match->venue,
    'Liiga' => $match->league,
  ]);
  $otherMatches = array_values(array_filter(
    array_map('skduo_match_data', skduo_get_upcoming_matches(4)),
    fn ($m) => $m->id !== $match->id
  ));
  $otherMatches = array_slice($otherMatches, 0, 3);
@endphp

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/single-mang.blade.php -->
  @endif

  <article class="bg-paper">
    <header class="relative overflow-hidden bg-hero-dark text-paper">
      <div class="mx-auto max-w-[1360px] px-4 pb-14 pt-12 sm:px-6 md:px-10 md:pb-20 md:pt-16">
        <a href="{{ esc_url(home_url('/kalender')) }}" class="mb-10 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-green-mint transition hover:text-lime">
          ← Kalender
        </a>

        <div class="mb-8 flex flex-wrap items-center gap-3 text-[0.65rem] font-extrabold uppercase tracking-[0.18em]">
          <span class="inline-flex rounded-full px-3 py-1 {{ $match->status === 'live' ? 'bg-coral text-paper' : ($isPast ? 'bg-white/10 text-paper/70' : 'bg-green-mint text-ink') }}">
            {{ $statusLabel }}
          </span>
          <span class="text-paper/70">{{ $isHome ? 'Kodumäng' : 'Võõrsilmäng' }}</span>
          @if($match->league)
            <span class="text-green-mint">{{ $match->league }}</span>
          @endif
        </div>

        {{-- Scoreboard --}}
        <div class="grid grid-cols-1 gap-6 border-y md:items-end border-white/10 py-10 md:grid-cols-[1fr_auto_1fr] md:gap-10 md:py-14">
          <div class="md:text-right">
            <div class="text-[0.65rem] font-extrabold uppercase tracking-[0.18em] text-paper/60">Kodu</div>
            <h1 class="mt-2 font-display text-5xl leading-[0.9] tracking-wide break-words sm:text-6xl lg:text-7xl {{ $isHome ? 'text-paper' : 'text-paper/80' }}">
              {{ $match->home ?: get_the_title() }}
            </h1>
          </div>

          <div class="text-center">
            @if($hasScore)
              <div class="font-display text-7xl leading-none text-lime sm:text-8xl lg:text-9xl">
                {{ $match->home_score }}<span class="mx-2 text-paper/40">:</span>{{ $match->away_score }}
              </div>
            @else
              <div class="font-display text-5xl leading-none text-green-mint sm:text-6xl">VS</div>
            @endif
          </div>

          <div>
            <div class="text-[0.65rem] font-extrabold uppercase tracking-[0.18em] text-paper/60">Külaline</div>
            <div class="mt-2 font-display text-5xl leading-[0.9] tracking-wide break-words sm:text-6xl lg:text-7xl {{ $isHome ? 'text-paper/80' : 'text-paper' }}">
              {{ $match->away }}
            </div>
          </div>
        </div>

        <div class="mt-10 space-y-10">
          <dl class="grid grid-cols-2 gap-x-6 gap-y-6 lg:grid-cols-4 lg:gap-x-10">
            @foreach($facts as $label => $value)
              <div>
                <dt class="text-[0.6rem] font-extrabold uppercase tracking-[0.18em] text-paper/60">{{ $label }}</dt>
                <dd class="mt-1 font-display text-2xl leading-none tracking-wide">{{ $value }}</dd>
              </div>
            @endforeach
          </dl>

          @if(($isPast || $match->status === 'finished') && ! empty($match->dataproject_url))
            <a href="{!! esc_url($match->dataproject_url) !!}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center bg-lime px-6 py-4 text-xs font-extrabold uppercase tracking-widest text-ink transition hover:bg-green-mint">
              Mängu statistika →
            </a>
          @endif

          @if($isUpcoming)
            <div class="flex flex-col gap-4 border-t border-white/10 pt-8 sm:flex-row sm:items-center sm:justify-between">
              {{-- Decorative live countdown; the date/time facts above carry the same info accessibly. --}}
              <div class="grid w-full max-w-sm grid-cols-4 gap-1.5" data-countdown-target="{{ $match->date_ts }}" aria-hidden="true">
                @foreach(['d' => 'päeva', 'h' => 'tundi', 'm' => 'min', 's' => 'sek'] as $key => $label)
                  <div class="border border-white/10 bg-white/5 px-2 py-2 text-center">
                    <div class="font-display text-xl leading-none" data-cd="{{ $key }}">--</div>
                    <div class="mt-1 text-[0.5rem] font-bold uppercase tracking-widest text-paper/60">{{ $label }}</div>
                  </div>
                @endforeach
              </div>

              @if($hasTicket)
                <a href="{{ esc_url($match->ticket_url) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center bg-lime px-6 py-4 text-xs font-extrabold uppercase tracking-widest text-ink transition hover:bg-green-mint">
                  Osta pilet →
                </a>
              @elseif($isFree)
                <span class="inline-flex items-center justify-center bg-lime px-6 py-4 text-xs font-extrabold uppercase tracking-widest text-ink">
                  Tasuta sissepääs
                </span>
              @endif
            </div>
          @endif
        </div>
      </div>
    </header>

    @if(trim((string) get_post_field('post_content', get_the_ID())) !== '')
      <section class="px-4 py-14 sm:px-6 md:px-10 md:py-20">
        <div class="skduo-article-content mx-auto max-w-4xl">
          @php
            the_content();
          @endphp
        </div>
      </section>
    @endif

    @if(! empty($otherMatches))
      <section class="border-t border-ink/10 px-4 py-14 sm:px-6 md:px-10 md:py-20">
        <div class="mx-auto max-w-[1360px]">
          <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <h2 class="font-display text-4xl leading-none tracking-wide text-ink sm:text-5xl">Järgmised mängud</h2>
            <a href="{{ esc_url(home_url('/kalender')) }}" class="text-xs font-extrabold uppercase tracking-widest text-green-deep transition hover:text-ink">
              Kogu kalender →
            </a>
          </div>
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($otherMatches as $other)
              <x-game-card :match="$other" />
            @endforeach
          </div>
        </div>
      </section>
    @endif
  </article>
@endsection
