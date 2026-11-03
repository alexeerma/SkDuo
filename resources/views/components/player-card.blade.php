{{--
  Reusable player / staff card for the teams page.
  Used as: <x-player-card :player="$player" />
--}}
@props(['player', 'showStats' => true])

@php
  $seasonImageAttributes = '';
  foreach (($player->season_images ?? []) as $seasonSlug => $seasonImage) {
    $seasonImageAttributes .= ' data-season-image-' . esc_attr($seasonSlug) . '="' . esc_url($seasonImage) . '"';
  }
@endphp

<a
  href="{{ esc_url($player->permalink) }}"
  {{ $attributes->merge(['class' => 'group relative block overflow-hidden border border-ink/10 bg-paper p-5 transition-all duration-300 hover:-translate-y-1 hover:border-green-deep hover:shadow-lg']) }}
  data-player-card
  data-seasons="{{ esc_attr($player->season_slugs ?? '') }}"
  data-default-image="{{ esc_url($player->image) }}"
  data-season-images="{{ esc_attr(wp_json_encode($player->season_images ?? [])) }}"
  {!! $seasonImageAttributes !!}
>
  <div class="absolute left-0 top-0 h-[3px] w-0 bg-green-deep transition-all duration-500 group-hover:w-full"></div>

  <div class="mb-5 flex items-start justify-between gap-4">
    @if($player->image)
      <img src="{{ esc_url($player->image) }}" alt="{{ esc_attr($player->name) }}" class="h-16 w-16 object-cover grayscale transition group-hover:grayscale-0" loading="lazy" data-player-image>
    @else
      <div class="grid h-16 w-16 place-items-center bg-ink text-paper">
        <span class="font-display text-3xl leading-none">{{ $player->initials }}</span>
      </div>
    @endif

    @if($player->number)
      <div class="font-display text-5xl leading-none text-green-deep/25 transition group-hover:text-green-deep">
        {{ $player->number }}
      </div>
    @endif
  </div>

  <div class="mb-5">
    <h3 class="font-display text-2xl leading-none tracking-wide text-ink sm:text-3xl">{{ $player->name }}</h3>
    @if($player->position)
      <p class="mt-2 text-xs font-extrabold uppercase tracking-widest text-green-deep">{{ $player->position }}</p>
    @endif
  </div>

  @if($showStats)
    <dl class="grid grid-cols-2 gap-2 border-t border-dashed border-ink/10 pt-4 text-xs">
      <div>
        <dt class="font-extrabold uppercase tracking-widest text-ink/62">Pikkus</dt>
        <dd class="mt-1 font-bold text-ink">{{ $player->height ?: '-' }}</dd>
      </div>
      <div>
        <dt class="font-extrabold uppercase tracking-widest text-ink/62">Sünd</dt>
        <dd class="mt-1 font-bold text-ink">{{ $player->year ?: '-' }}</dd>
      </div>
    </dl>
  @endif
</a>
