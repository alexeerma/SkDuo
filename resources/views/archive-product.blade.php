{{--
  WooCommerce product archive / merch catalog. Data: App\View\Composers\Shop.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/archive-product.blade.php -->
  @endif

  <section class="bg-[var(--skduo-hero-bg)] px-4 pb-10 pt-12 text-paper sm:px-6 md:px-12 md:pb-14 md:pt-16" data-product-archive-hero>
    <div class="mx-auto max-w-[1360px]">
      <span class="mb-5 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.2em] text-green-mint">
        <span class="h-px w-8 bg-green-mint"></span>
        {{ $pageOptions['hero_eyebrow'] ?? 'SK DUO merch' }}
      </span>

      <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(0,26rem)] lg:items-end lg:gap-12">
        <h1 class="font-serif-display text-[clamp(2.75rem,8vw,5.5rem)] leading-[0.92] tracking-normal text-balance">
          {{ $archiveTitle }}
          @if(! empty($pageOptions['hero_emphasis']))
            <em class="text-green-mint">{{ $pageOptions['hero_emphasis'] }}</em>
          @endif
        </h1>

        @if($archiveDescription)
          <p class="max-w-xl text-sm leading-relaxed text-paper/70 md:text-base">
            {{ $archiveDescription }}
          </p>
        @endif
      </div>
    </div>
  </section>

  <section class="bg-paper px-4 pb-16 pt-6 text-ink sm:px-6 md:px-12 md:pb-24 md:pt-8" data-product-archive>
    <div class="mx-auto max-w-[1360px]">
      <div class="mb-7 flex flex-wrap items-center justify-between gap-x-6 gap-y-3 border-b border-ink/10 pb-5 md:mb-10">
        @if(! empty($categories))
          <nav class="skduo-shop-filters" aria-label="{{ esc_attr__('Tootekategooriad', 'skduo') }}">
            <a href="{{ esc_url($shopUrl) }}" data-product-archive-link class="skduo-shop-pill" @if($isAllActive) aria-current="page" @endif>
              {{ $pageOptions['filter_all_label'] ?? 'Kõik' }}
            </a>
            @foreach($categories as $category)
              <a href="{{ esc_url($category['url']) }}" data-product-archive-link class="skduo-shop-pill" @if($category['active']) aria-current="page" @endif>
                {{ $category['name'] }}
              </a>
            @endforeach
          </nav>
        @endif

        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-ink/62">
          <span class="font-display text-base tracking-[0.04em] text-ink">{{ $productCount }}</span>
          {{ $pageOptions['catalog_count_label'] ?? 'toodet' }}
        </p>
      </div>

      @if(! empty($productCards))
        <div class="grid grid-cols-2 gap-x-3 gap-y-9 sm:gap-x-5 lg:grid-cols-3 lg:gap-y-12 xl:grid-cols-4">
          @foreach($productCards as $card)
            @include('partials.product-card', ['card' => $card])
          @endforeach
        </div>

        @if($pagination)
          <nav class="skduo-shop-pagination mt-12 md:mt-16" aria-label="{{ esc_attr__('Lehekülgede navigeerimine', 'skduo') }}" data-product-pagination>
            {!! $pagination !!}
          </nav>
        @endif
      @else
        <div class="rounded-[18px] border border-dashed border-ink/20 p-10 text-center md:rounded-[28px]">
          <h2 class="font-serif-display text-4xl leading-none tracking-normal text-ink md:text-5xl">
            {{ $pageOptions['empty_title'] ?? 'Tooteid ei ole veel lisatud.' }}
          </h2>
          @if(! empty($pageOptions['empty_text']))
            <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-ink/60">
              {{ $pageOptions['empty_text'] }}
            </p>
          @endif
        </div>
      @endif
    </div>
  </section>
@endsection
