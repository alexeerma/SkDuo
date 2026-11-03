{{--
  Reusable game card.
  Used as: <x-game-card :match="$match" />
--}}
@props(['match'])

@php
  $isHome = $match->is_home;
  $monthShort = strtoupper(wp_date('M', $match->date_ts));
  $day = wp_date('d', $match->date_ts);
  $dow = wp_date('l', $match->date_ts);
  $time = wp_date('H:i', $match->date_ts);
  $isPast = $match->date_ts && $match->date_ts < time();
  $isFree = ! empty($match->free_entry) && ! $isPast;
  $hasTicket = $isHome && $match->ticket_url && ! $isFree && ! $isPast;
@endphp

{{-- Whole card links to the match page (stretched Info link); the ticket button sits above it. --}}
<article
  {{ $attributes->merge(['class' => 'group flex flex-col bg-paper border border-ink/10 p-4 sm:p-5 relative rounded transition-all duration-300 hover:border-green-deep hover:-translate-y-1 hover:shadow-lg overflow-hidden focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-within:outline focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-green-deep']) }}
>
  {{-- Animated top border --}}
  <div class="absolute top-0 left-0 h-[3px] bg-green-deep w-0 group-hover:w-full transition-all duration-500"></div>

  {{-- Home/Away badge --}}
  <span class="absolute top-3 right-3 text-[0.55rem] font-extrabold uppercase tracking-widest px-2 py-0.5 rounded
    {{ $isHome ? 'bg-green-deep text-paper' : 'bg-ink text-paper' }}">
    {{ $isHome ? 'Kodu' : 'Võõr' }}
  </span>

  <div class="font-display text-3xl leading-none pr-14">
    {{ $day }}<span class="text-green-deep text-base ml-1">{{ $monthShort }}</span>
  </div>
  <div class="text-[0.68rem] font-bold uppercase tracking-widest text-ink/60 mt-1 mb-4">
    {{ $dow }}
  </div>

  <div class="space-y-1 mb-3">
    <div class="text-sm font-bold {{ $isHome ? 'text-green-deep' : 'text-ink' }}">{{ $match->home }}</div>
    <div class="text-xs uppercase text-ink/60 tracking-wider">vs</div>
    <div class="text-sm font-bold {{ $isHome ? 'text-ink' : 'text-green-deep' }}">{{ $match->away }}</div>
  </div>

  <div class="flex-1 text-xs text-ink/60 mb-3 pb-3 border-b border-dashed border-ink/10 leading-snug">
    {{ $match->venue }} · {{ $match->league }}
  </div>

  <div class="flex items-center justify-between gap-3 text-xs font-extrabold uppercase tracking-widest">
    <span class="font-display text-lg text-ink">{{ $time }}</span>
    <div class="flex items-center gap-2">
      <a href="{{ esc_url(get_permalink($match->id)) }}" class="-my-2 inline-block px-1 py-2 text-ink/60 transition after:absolute after:inset-0 group-hover:text-green-deep focus-visible:outline-none">
        Info →
      </a>
      @if($hasTicket)
        <a href="{{ esc_url($match->ticket_url) }}" target="_blank" rel="noopener" class="relative z-10 rounded bg-green-deep px-3 py-1.5 text-paper transition hover:bg-ink">
          Pilet →
        </a>
      @elseif($isFree)
        <span class="rounded bg-lime px-2 py-1 text-[0.6rem] text-ink">Tasuta</span>
      @endif
    </div>
  </div>
</article>
