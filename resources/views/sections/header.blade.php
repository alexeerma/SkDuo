{{--
  Site header — top announce bar + main nav.
--}}

{{-- Announcement marquee --}}
@php
  $tickerItems = skduo_get_header_ticker_items();
  $tickerText = implode(' · ', array_map(function ($item) {
    return is_array($item) ? (string) ($item['text'] ?? '') : (string) $item;
  }, $tickerItems));
  $showHeaderTicker = function_exists('skduo_should_show_header_ticker') ? skduo_should_show_header_ticker() : true;
@endphp
@if($showHeaderTicker)
  <div class="bg-lime border-b border-ink overflow-hidden">
    <span class="sr-only">{{ $tickerText }}</span>
    <div class="skduo-announcement-marquee py-2 text-xs font-extrabold uppercase tracking-widest">
      @foreach(range(1, 2) as $loopIndex)
        <div class="skduo-announcement-track" aria-hidden="true">
          @foreach(range(1, 4) as $repeatIndex)
            @foreach($tickerItems as $item)
              @php
                $tickerLabel = is_array($item) ? (string) ($item['text'] ?? '') : (string) $item;
                $tickerUrl = is_array($item) ? trim((string) ($item['url'] ?? '')) : '';
                $tickerHost = $tickerUrl ? parse_url($tickerUrl, PHP_URL_HOST) : '';
                $tickerHomeHost = parse_url(home_url('/'), PHP_URL_HOST);
                $tickerIsExternal = $tickerHost && $tickerHost !== $tickerHomeHost;
              @endphp
              @if($tickerUrl)
                <a
                  href="{{ esc_url($tickerUrl) }}"
                  class="skduo-announcement-link inline-flex items-center gap-2"
                  @if($tickerIsExternal) target="_blank" rel="noopener" @endif
                >{{ $tickerLabel }}</a>
              @else
                <span class="inline-flex items-center gap-2">{{ $tickerLabel }}</span>
              @endif
            @endforeach
          @endforeach
        </div>
      @endforeach
    </div>
  </div>
@endif

{{-- Top contact bar --}}
@php
  $contactBar = skduo_get_contact_bar_data();
@endphp
<div class="bg-ink text-paper px-4 py-2 text-[0.68rem] font-medium uppercase tracking-wider md:px-8">
  <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 md:justify-between">
    <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 md:justify-start">
    @if($contactBar->live_enabled && $contactBar->live_text !== '')
      @if($contactBar->live_url !== '')
        <a
          href="{{ esc_url($contactBar->live_url) }}"
          class="hidden items-center gap-2 rounded text-green-mint transition hover:text-lime focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-mint sm:flex"
          target="_blank"
          rel="noopener"
        >
          <span class="w-2 h-2 bg-green-mint rounded-full animate-pulse" aria-hidden="true"></span>
          {{ $contactBar->live_text }}
        </a>
      @else
        <span class="hidden items-center gap-2 text-green-mint sm:flex">
          <span class="w-2 h-2 bg-green-mint rounded-full animate-pulse" aria-hidden="true"></span>
          {{ $contactBar->live_text }}
        </span>
      @endif
    @endif
    @if($contactBar->phone !== '')
      <a href="tel:{{ preg_replace('/\s+/', '', $contactBar->phone) }}" class="transition hover:text-green-mint">{{ $contactBar->phone }}</a>
    @endif
    @if($contactBar->email !== '')
      <a href="mailto:{{ $contactBar->email }}" class="transition hover:text-green-mint">{{ $contactBar->email }}</a>
    @endif
  </div>
  @php
    $socialLinks = skduo_get_social_links();
  @endphp
  @if(! empty($socialLinks))
    <div class="flex gap-2">
      @foreach($socialLinks as $platform => $social)
        <a
          href="{{ esc_url($social['url']) }}"
          class="relative grid h-6 w-6 place-items-center rounded-full border border-paper/30 text-paper/80 transition before:absolute before:-inset-2.5 before:content-[''] hover:border-green-mint hover:bg-green-mint hover:text-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-mint"
          aria-label="{{ esc_attr($social['label']) }}"
          @if($social['url'] !== '#') target="_blank" rel="noopener" @endif
        >
          {!! skduo_social_icon($platform) !!}
        </a>
      @endforeach
    </div>
  @endif
  </div>
</div>

{{-- Main nav --}}
@php
  $locations = get_nav_menu_locations();
  $menuId = $locations['primary_navigation'] ?? null;
  $rawMenuItems = $menuId ? wp_get_nav_menu_items($menuId) : [];
  $rawMenuItems = is_array($rawMenuItems) ? $rawMenuItems : [];
  $menuChildren = [];

  foreach ($rawMenuItems as $item) {
    $parentId = (int) $item->menu_item_parent;
    if ($parentId > 0) {
      $menuChildren[$parentId][] = $item;
    }
  }

  $menuItems = array_values(array_filter($rawMenuItems, function ($item) {
    return (int) $item->menu_item_parent === 0;
  }));

  $fallbackMenu = [
    ['url' => home_url('/'), 'title' => 'Esileht'],
    ['url' => home_url('/meeskonnad'), 'title' => 'Võistkonnad'],
    ['url' => home_url('/noored'), 'title' => 'Noored'],
    ['url' => home_url('/kalender'), 'title' => 'Kalender'],
    ['url' => home_url('/uudised'), 'title' => 'Uudised'],
    ['url' => home_url('/toetajad'), 'title' => 'Toetajad'],
    ['url' => home_url('/epood'), 'title' => 'E-pood'],
    ['url' => home_url('/klubist'), 'title' => 'Klubist'],
  ];

  $currentPath = trim(request()->path(), '/');
  $homeHost = parse_url(home_url('/'), PHP_URL_HOST);
  $isCurrentUrl = function ($url) use ($currentPath, $homeHost) {
    $host = parse_url($url, PHP_URL_HOST);
    $path = trim(parse_url($url, PHP_URL_PATH) ?: '', '/');

    return (! $host || $host === $homeHost) && $currentPath === $path;
  };

  $headerCta = skduo_get_header_cta();
  $headerCtaHost = parse_url($headerCta['url'], PHP_URL_HOST);
  $headerCtaIsExternal = $headerCtaHost && $headerCtaHost !== $homeHost;
  $headerBrand = function_exists('skduo_get_header_brand') ? skduo_get_header_brand() : [
    'show_text' => false,
    'title' => get_bloginfo('name') ?: 'SK DUO',
    'subtitle' => get_bloginfo('description') ?: 'Tartu Võrkpalliklubi · Est. 1999',
  ];
@endphp

<nav data-site-nav class="sticky top-0 z-50 border-b border-ink/10 bg-paper/92 px-4 py-3 backdrop-blur-xl md:px-8 md:py-4">
  <div class="flex items-center justify-between gap-4">
  <a href="{{ home_url('/') }}" class="flex min-w-0 items-center gap-3">
    {!! function_exists('skduo_header_logo_markup') ? skduo_header_logo_markup() : skduo_site_logo_markup('skduo-site-logo skduo-header-logo') !!}
    @if($headerBrand['show_text'])
      <div class="min-w-0 font-display text-xl leading-none tracking-wider md:text-2xl">
        {{ $headerBrand['title'] }}
        @if(! empty($headerBrand['subtitle']))
          <span class="mt-0.5 hidden truncate font-sans text-[0.55rem] font-bold uppercase tracking-widest text-ink/60 sm:block">{{ $headerBrand['subtitle'] }}</span>
        @endif
      </div>
    @endif
  </a>

  <ul class="hidden list-none items-center gap-5 lg:flex xl:gap-6">
    @forelse($menuItems as $item)
      @php
        $children = $menuChildren[(int) $item->ID] ?? [];
        $isActiveParent = $isCurrentUrl($item->url) || collect($children)->contains(fn ($child) => $isCurrentUrl($child->url));
      @endphp
      <li class="group relative">
        <a href="{{ esc_url($item->url) }}"
           @if($item->target) target="{{ esc_attr($item->target) }}" rel="noopener" @endif
           class="inline-flex items-center gap-2 rounded py-4 text-xs font-bold uppercase tracking-widest text-ink transition-colors hover:text-green-deep focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-deep
                  @if($isActiveParent) text-green-deep @endif">
          {{ $item->title }}
          @if(! empty($children))
            <span class="mt-[-0.18rem] h-1.5 w-1.5 rotate-45 border-b-2 border-r-2 border-green-deep transition group-hover:mt-0" aria-hidden="true"></span>
          @endif
        </a>

        @if(! empty($children))
          <div class="invisible absolute right-0 top-full z-50 min-w-56 translate-y-2 border border-ink/10 bg-paper p-2 opacity-0 shadow-[0_24px_60px_rgba(10,21,24,.16)] transition group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
            <ul class="grid list-none gap-1">
              @foreach($children as $child)
                <li>
                  <a href="{{ esc_url($child->url) }}"
                     @if($child->target) target="{{ esc_attr($child->target) }}" rel="noopener" @endif
                     class="block whitespace-nowrap px-4 py-3 text-xs font-extrabold uppercase tracking-[0.14em] text-ink/68 transition hover:bg-green-deep hover:text-paper focus-visible:bg-green-deep focus-visible:text-paper focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-green-deep @if($isCurrentUrl($child->url)) bg-green-deep text-paper @endif">
                    {{ $child->title }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        @endif
      </li>
    @empty
      @foreach($fallbackMenu as $item)
        <li>
          <a href="{{ esc_url($item['url']) }}"
             class="text-xs font-bold uppercase tracking-widest text-ink hover:text-green-deep transition-colors relative
                    @if($isCurrentUrl($item['url'])) text-green-deep @endif">
            {{ $item['title'] }}
          </a>
        </li>
      @endforeach
    @endforelse
  </ul>

  <div class="hidden items-center gap-2 lg:flex">
    <a
      href="{{ esc_url($headerCta['url']) }}"
      class="btn-primary shrink-0 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ink"
      data-magnet="0.3"
      @if($headerCtaIsExternal) target="_blank" rel="noopener" @endif
    >
      {{ $headerCta['label'] }} →
    </a>
  </div>

  <div class="flex shrink-0 items-center gap-2 lg:hidden">
    <a
      href="{{ esc_url($headerCta['url']) }}"
      class="inline-flex h-11 min-w-[5.75rem] items-center justify-center rounded-full bg-green-deep px-4 text-[0.68rem] font-extrabold uppercase tracking-[0.14em] text-paper transition hover:bg-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ink"
      @if($headerCtaIsExternal) target="_blank" rel="noopener" @endif
    >
      {{ $headerCta['label'] }}
    </a>

    <details class="skduo-mobile-menu group">
      <summary class="relative z-[70] flex h-11 min-w-[5.75rem] cursor-pointer list-none items-center justify-center gap-2 rounded-full border border-ink/15 px-4 text-[0.68rem] font-extrabold uppercase tracking-[0.14em] text-ink transition hover:border-green-deep hover:text-green-deep focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-deep group-open:fixed group-open:right-4 group-open:top-[23px] group-open:border-paper/30 group-open:bg-paper group-open:text-ink group-open:shadow-[0_16px_45px_rgba(10,21,24,.16)]">
        <span class="relative h-3.5 w-4" aria-hidden="true">
          <span class="absolute left-0 top-0 block h-0.5 w-4 rounded-full bg-current transition group-open:top-1.5 group-open:rotate-45"></span>
          <span class="absolute left-0 top-1.5 block h-0.5 w-4 rounded-full bg-current transition group-open:opacity-0"></span>
          <span class="absolute left-0 top-3 block h-0.5 w-4 rounded-full bg-current transition group-open:top-1.5 group-open:-rotate-45"></span>
        </span>
        <span>Menüü</span>
      </summary>

      <div class="skduo-mobile-menu__panel fixed inset-0 z-[60] flex w-screen flex-col bg-paper px-4 pb-8 pt-24 shadow-[0_24px_60px_rgba(10,21,24,.16)]">
        <a href="{{ home_url('/') }}" class="fixed left-4 top-3 z-[65] flex min-w-0 items-center">
          {!! function_exists('skduo_header_logo_markup') ? skduo_header_logo_markup() : skduo_site_logo_markup('skduo-site-logo skduo-header-logo') !!}
        </a>

        <ul class="mx-auto grid w-full max-w-lg list-none gap-2">
          @forelse($menuItems as $item)
            @php
              $children = $menuChildren[(int) $item->ID] ?? [];
              $isActiveParent = $isCurrentUrl($item->url) || collect($children)->contains(fn ($child) => $isCurrentUrl($child->url));
            @endphp
            <li>
              <a href="{{ esc_url($item->url) }}"
                 @if($item->target) target="{{ esc_attr($item->target) }}" rel="noopener" @endif
                 class="flex items-center justify-between gap-4 border border-ink/10 bg-white px-5 py-5 text-base font-extrabold uppercase tracking-[0.14em] text-ink transition hover:border-green-deep/30 hover:bg-green-deep hover:text-paper focus-visible:border-green-deep/30 focus-visible:bg-green-deep focus-visible:text-paper focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-deep
                        @if($isActiveParent) text-green-deep @endif">
                <span>{{ $item->title }}</span>
                @if(! empty($children))
                  <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-green-deep/30 bg-green-mint/10 text-green-deep" aria-hidden="true">
                    <span class="mt-[-0.18rem] h-2 w-2 rotate-45 border-b-2 border-r-2 border-current"></span>
                  </span>
                @endif
              </a>
              @if(! empty($children))
                <ul class="grid list-none gap-1 border-x border-b border-ink/10 bg-cream px-4 py-3">
                  @foreach($children as $child)
                    <li>
                      <a href="{{ esc_url($child->url) }}"
                         @if($child->target) target="{{ esc_attr($child->target) }}" rel="noopener" @endif
                         class="flex items-center gap-3 rounded px-3 py-2 text-sm font-extrabold uppercase tracking-[0.12em] text-ink/58 transition hover:text-green-deep focus-visible:text-green-deep focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-deep @if($isCurrentUrl($child->url)) text-green-deep @endif">
                        <span class="h-px w-5 shrink-0 bg-green-deep/45" aria-hidden="true"></span>
                        <span>{{ $child->title }}</span>
                      </a>
                    </li>
                  @endforeach
                </ul>
              @endif
            </li>
          @empty
            @foreach($fallbackMenu as $item)
              <li>
                <a href="{{ esc_url($item['url']) }}"
                   class="block border border-ink/10 bg-white px-5 py-5 text-base font-extrabold uppercase tracking-[0.14em] text-ink transition hover:border-green-deep/30 hover:bg-green-deep hover:text-paper
                          @if($isCurrentUrl($item['url'])) text-green-deep @endif">
                  {{ $item['title'] }}
                </a>
              </li>
            @endforeach
          @endforelse
        </ul>

        @php
          $overlaySocialLinks = function_exists('skduo_get_social_links') ? skduo_get_social_links() : [];
        @endphp
        @if(! empty($overlaySocialLinks))
          <div class="mx-auto mt-auto w-full max-w-lg border-t border-ink/10 pt-8">
            <div class="mb-4 text-center text-[0.68rem] font-extrabold uppercase tracking-[0.18em] text-ink/45">Jälgi meid</div>
            <div class="flex justify-center gap-3">
            @foreach($overlaySocialLinks as $platform => $social)
              <a
                href="{{ esc_url($social['url']) }}"
                class="grid h-12 w-12 place-items-center rounded-full border border-ink/15 bg-white text-ink transition hover:border-green-deep hover:bg-green-deep hover:text-paper focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-deep"
                aria-label="{{ esc_attr($social['label']) }}"
                @if($social['url'] !== '#') target="_blank" rel="noopener" @endif
              >
                {!! skduo_social_icon($platform) !!}
              </a>
            @endforeach
            </div>
          </div>
        @endif
      </div>
    </details>
  </div>
  </div>
</nav>
