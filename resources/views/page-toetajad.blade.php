{{--
  Sponsors page (page-toetajad.blade.php).
  Data pulled from the 'toetaja' CPT, grouped by _tier meta.
--}}
@extends('layouts.app')

@php
  $pageOptions = function_exists('skduo_get_page_copy_options') ? skduo_get_page_copy_options('toetajad') : [];
  $heroLines = array_values(array_filter(array_map('trim', preg_split('/\R+/', (string) ($pageOptions['hero_title'] ?? '')) ?: [])));
  $heroEmphasisLine = max(1, (int) ($pageOptions['hero_emphasis_line'] ?? 2));

  $allSponsors = get_posts([
    'post_type'      => 'toetaja',
    'posts_per_page' => -1,
    'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
    'order'          => 'ASC',
  ]);

  $byTier = ['nimisponsor' => [], 'sponsor' => [], 'partner' => []];
  foreach ($allSponsors as $s) {
    $t = (string) get_post_meta($s->ID, '_tier', true);
    $t = array_key_exists($t, $byTier) ? $t : 'sponsor';

    $byTier[$t][] = (object) [
      'id'            => $s->ID,
      'name'          => get_the_title($s),
      'website'       => (string) get_post_meta($s->ID, '_website', true),
      'description'   => (string) get_post_meta($s->ID, '_description', true),
      'partner_label' => (string) get_post_meta($s->ID, '_partner_label', true),
      'logo'          => get_the_post_thumbnail_url($s, 'large') ?: '',
      'logo_small'    => get_the_post_thumbnail_url($s, 'medium') ?: '',
    ];
  }

  $nameSponsors   = $byTier['nimisponsor'];
  $sponsors       = $byTier['sponsor'];
  $partners       = $byTier['partner'];
  $nameTierLabel = trim((string) ($pageOptions['name_tier_label'] ?? 'Nimisponsorid'));
  $nameTierLabel = in_array($nameTierLabel, ['Nimisponsor', 'Nimisponsori'], true) ? 'Nimisponsorid' : $nameTierLabel;
  $nameSectionLabel = trim((string) ($pageOptions['name_section_label'] ?? 'NIMISPONSORID'));
  $nameSectionLabel = $nameSectionLabel === '★ NIMISPONSOR ★' ? 'NIMISPONSORID' : $nameSectionLabel;
  $partnersTierLabel = trim((string) ($pageOptions['partners_tier_label'] ?? 'Partnerid'));
@endphp

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/page-toetajad.blade.php -->
  @endif

  {{-- HERO --}}
  <section class="relative overflow-hidden bg-ink text-paper">
    {{-- Grid overlay --}}
    {{-- Bottom fade --}}
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-ink to-transparent"></div>

    <div class="relative z-10 flex min-h-[62vh] flex-col justify-between px-4 py-12 sm:px-6 md:min-h-[72vh] md:px-12 md:py-20">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <span class="inline-flex items-center gap-2 text-[0.65rem] font-extrabold uppercase tracking-[0.22em] text-green-mint">
          <span class="h-px w-8 bg-green-mint"></span>
          {{ $pageOptions['hero_eyebrow'] ?? 'SK DUO · Partnerid' }}
        </span>
        <span class="text-[0.65rem] font-bold uppercase tracking-widest text-paper/60">
          {{ count($allSponsors) }} {{ $pageOptions['partner_count_label'] ?? 'koostööpartnerit' }}
        </span>
      </div>

      <div class="mt-auto max-w-5xl pt-20 md:pt-24">
        <h1 class="font-serif-display text-[clamp(3rem,16vw,8rem)] leading-[0.9] tracking-normal">
          @foreach($heroLines as $line)
            @if($loop->iteration === $heroEmphasisLine)
              <em class="text-green-mint">{{ $line }}</em>
            @else
              {{ $line }}
            @endif
            @if(! $loop->last)<br>@endif
          @endforeach
        </h1>
        <p class="mt-8 max-w-xl text-base leading-relaxed text-paper/60 md:text-lg">
          {{ $pageOptions['hero_text'] ?? '' }}
        </p>
      </div>

      <div class="mt-12 flex flex-wrap gap-3 border-t border-white/10 pt-6 md:mt-14 md:pt-8">
        @foreach([
          ['label' => $nameTierLabel,                                  'count' => count($nameSponsors), 'accent' => 'border-lime text-lime'],
          ['label' => $partnersTierLabel,                              'count' => count($partners),      'accent' => 'border-paper/40 text-paper/70'],
          ['label' => $pageOptions['sponsors_tier_label'] ?? 'Sponsorid', 'count' => count($sponsors),     'accent' => 'border-green-mint text-green-mint'],
        ] as $tier)
          <div class="flex items-center gap-3 border {{ $tier['accent'] }} px-4 py-2">
            <span class="font-display text-2xl leading-none">{{ $tier['count'] }}</span>
            <span class="text-[0.6rem] font-extrabold uppercase tracking-widest opacity-70">{{ $tier['label'] }}</span>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- NAME SPONSOR --}}
  @if(! empty($nameSponsors))
    <section class="bg-ink text-paper">
      <div class="border-t border-white/10 px-4 pb-20 pt-16 sm:px-6 md:px-12 md:pb-24 md:pt-20">
        <div class="mb-10 text-center md:mb-16" data-reveal>
          <span class="inline-block bg-lime px-3 py-1.5 font-display text-[0.7rem] tracking-[0.3em] text-ink uppercase">
            {{ $nameSectionLabel }}
          </span>
        </div>

        @foreach($nameSponsors as $sponsor)
          <div class="mx-auto max-w-4xl" data-reveal>
            @if($sponsor->website)
              <a href="{{ esc_url($sponsor->website) }}" target="_blank" rel="noopener" class="group block">
            @else
              <div>
            @endif

            <div class="relative border border-white/10 bg-white/[0.03] p-6 text-center transition duration-500 group-hover:bg-white/[0.06] sm:p-10 md:p-20">
              {{-- Corner accents --}}
              <span class="absolute left-0 top-0 block h-8 w-8 border-l-2 border-t-2 border-lime transition-all duration-500 group-hover:h-12 group-hover:w-12"></span>
              <span class="absolute right-0 top-0 block h-8 w-8 border-r-2 border-t-2 border-lime transition-all duration-500 group-hover:h-12 group-hover:w-12"></span>
              <span class="absolute bottom-0 left-0 block h-8 w-8 border-b-2 border-l-2 border-lime transition-all duration-500 group-hover:h-12 group-hover:w-12"></span>
              <span class="absolute bottom-0 right-0 block h-8 w-8 border-b-2 border-r-2 border-lime transition-all duration-500 group-hover:h-12 group-hover:w-12"></span>

              @if($sponsor->logo)
                <div class="mx-auto mb-10 flex h-32 max-w-xs items-center justify-center md:h-40">
                  <img
                    src="{{ esc_url($sponsor->logo) }}"
                    alt="{{ esc_attr($sponsor->name) }}"
                    class="max-h-full max-w-full object-contain transition duration-500 group-hover:scale-105"
                    loading="eager"
                  >
                </div>
              @endif

              <h2 class="font-display text-5xl leading-none tracking-wide sm:text-6xl md:text-8xl">{{ $sponsor->name }}</h2>

              @if($sponsor->description)
                <p class="mx-auto mt-6 max-w-lg text-base leading-relaxed text-paper/55">{{ $sponsor->description }}</p>
              @endif

              @if($sponsor->website)
                <span class="mt-8 inline-flex items-center gap-2 text-[0.65rem] font-extrabold uppercase tracking-[0.2em] text-lime transition-all duration-300 group-hover:gap-4">
                  Külasta veebilehte <span aria-hidden="true">→</span>
                </span>
              @endif
            </div>

            @if($sponsor->website) </a> @else </div> @endif
          </div>
        @endforeach
      </div>
    </section>
  @endif

  {{-- PARTNERS --}}
  @if(! empty($partners))
    <section class="bg-ink text-paper">
      <div class="border-t border-white/10 px-4 py-14 sm:px-6 md:px-12 md:py-20">

        <div class="mb-10 flex flex-col gap-6 md:mb-12 md:flex-row md:items-end md:justify-between" data-reveal>
          <div>
            <span class="mb-2 inline-flex items-center gap-2 text-[0.65rem] font-extrabold uppercase tracking-[0.2em] text-green-mint">
              <span class="h-px w-7 bg-green-mint"></span>
              {{ $pageOptions['partners_eyebrow'] ?? 'Partnerid' }}
            </span>
            <h2 class="font-display text-4xl leading-none tracking-wide sm:text-5xl md:text-6xl">{{ $pageOptions['partners_title'] ?? 'PARTNERID' }}</h2>
          </div>
          <div class="hidden h-px flex-1 bg-white/10 md:block"></div>
        </div>

        @php
          // Fewer than 3 partners: shrink the grid to the cards' 3-col width and center it, so no empty grey cell shows.
          $partnerGridClass = match (count($partners)) {
            1 => 'mx-auto max-w-md',
            2 => 'sm:grid-cols-2 lg:mx-auto lg:max-w-[66.666%]',
            default => 'sm:grid-cols-2 lg:grid-cols-3',
          };
        @endphp
        <div class="grid gap-px border border-white/10 bg-white/10 {{ $partnerGridClass }}">
          @foreach($partners as $partner)
            @if($partner->website)
              <a href="{{ esc_url($partner->website) }}" target="_blank" rel="noopener"
                 class="group relative flex flex-col bg-ink transition duration-300 hover:bg-green-mint hover:text-ink"
                 data-reveal data-reveal-delay="{{ $loop->index * 60 }}">
            @else
              <div class="group relative flex flex-col bg-ink" data-reveal data-reveal-delay="{{ $loop->index * 60 }}">
            @endif

            <div class="flex flex-1 items-center justify-center px-6 py-10 sm:px-10 sm:py-12">
              @if($partner->logo_small)
                <img
                  src="{{ esc_url($partner->logo_small) }}"
                  alt=""
                  class="h-16 max-w-[200px] object-contain transition duration-300 group-hover:scale-105"
                  loading="lazy"
                >
              @else
                <span class="font-display text-4xl leading-none tracking-wide text-paper/30 transition group-hover:text-ink/50" aria-hidden="true">
                  {{ $partner->name }}
                </span>
              @endif
            </div>

            <div class="border-t border-white/10 px-6 py-4 transition group-hover:border-ink/20">
              <div class="font-display text-lg leading-none tracking-wide">{{ $partner->name }}</div>
              @if($partner->partner_label)
                <div class="mt-1 text-[0.6rem] font-bold uppercase tracking-widest text-green-mint transition group-hover:text-ink/60">
                  {{ $partner->partner_label }}
                </div>
              @endif
            </div>

            @if($partner->website) </a> @else </div> @endif
          @endforeach
        </div>

      </div>
    </section>
  @endif

  {{-- SPONSORS --}}
  @if(! empty($sponsors))
    <section class="bg-paper">
      <div class="border-t border-ink/10 px-4 py-14 sm:px-6 md:px-12 md:py-20">

        <div class="mb-10 flex flex-col gap-6 md:mb-12 md:flex-row md:items-end md:justify-between" data-reveal>
          <div>
            <span class="mb-2 inline-flex items-center gap-2 text-[0.65rem] font-extrabold uppercase tracking-[0.2em] text-green-deep">
              <span class="h-px w-7 bg-green-deep"></span>
              {{ $pageOptions['sponsors_eyebrow'] ?? 'Toetajad' }}
            </span>
            <h2 class="font-display text-4xl leading-none tracking-wide sm:text-5xl md:text-6xl">{{ $pageOptions['sponsors_title'] ?? 'SPONSORID' }}</h2>
          </div>
          <div class="hidden h-px flex-1 bg-ink/10 md:block"></div>
        </div>

        <div class="grid gap-px border border-ink/10 bg-ink/10 sm:grid-cols-2 lg:grid-cols-3">
          @foreach($sponsors as $sponsor)
            @if($sponsor->website)
              <a href="{{ esc_url($sponsor->website) }}" target="_blank" rel="noopener"
                 class="group relative flex flex-col bg-paper transition duration-300 hover:bg-green-deep hover:text-paper"
                 data-reveal data-reveal-delay="{{ $loop->index * 60 }}">
            @else
              <div class="group relative flex flex-col bg-paper" data-reveal data-reveal-delay="{{ $loop->index * 60 }}">
            @endif

            <div class="flex flex-1 items-center justify-center px-6 py-10 sm:px-10 sm:py-12">
              @if($sponsor->logo_small)
                <img
                  src="{{ esc_url($sponsor->logo_small) }}"
                  alt=""
                  class="h-16 max-w-[200px] object-contain transition duration-300 group-hover:scale-105"
                  loading="lazy"
                >
              @else
                <span class="font-display text-4xl leading-none tracking-wide text-ink/30 transition group-hover:text-paper/50" aria-hidden="true">
                  {{ $sponsor->name }}
                </span>
              @endif
            </div>

            <div class="border-t border-ink/10 px-6 py-4 transition group-hover:border-white/20">
              <div class="font-display text-lg leading-none tracking-wide">{{ $sponsor->name }}</div>
              @if($sponsor->website)
                <div class="mt-1 text-[0.6rem] font-bold uppercase tracking-widest text-ink/62 transition group-hover:text-paper/70">
                  {{ parse_url($sponsor->website, PHP_URL_HOST) ?: '' }}
                </div>
              @endif
            </div>

            @if($sponsor->website) </a> @else </div> @endif
          @endforeach
        </div>

      </div>
    </section>
  @endif

  {{-- EMPTY STATE --}}
  @if(empty($allSponsors))
    <section class="bg-paper px-4 py-20 sm:px-6 md:px-12 md:py-24">
      <div class="max-w-xl border border-dashed border-ink/20 p-10">
        <span class="mb-3 inline-flex items-center gap-2 text-[0.65rem] font-extrabold uppercase tracking-widest text-green-deep">
          <span class="h-px w-7 bg-green-deep"></span>
          {{ $pageOptions['empty_eyebrow'] ?? 'Sisu puudub' }}
        </span>
        <h2 class="mb-4 font-display text-4xl leading-none tracking-wide">{{ $pageOptions['empty_title'] ?? '' }}</h2>
        <p class="text-sm leading-relaxed text-ink/60">
          {{ $pageOptions['empty_text'] ?? '' }}
        </p>
      </div>
    </section>
  @endif

@endsection
