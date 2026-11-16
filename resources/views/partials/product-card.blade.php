{{--
  Product card used by the shop archive and the related products row.
  Expects: $card (array from skduo_product_card_data()), optional $headingTag (default h2).
--}}
@php($headingTag = $headingTag ?? 'h2')

<article class="skduo-product-card group relative flex flex-col">
  <div class="skduo-product-card__media relative aspect-[4/5] overflow-hidden rounded-[18px] md:rounded-[24px]">
    @if($card['image_html'])
      {!! $card['image_html'] !!}
    @else
      <div class="grid h-full w-full place-items-center bg-card-fallback" style="--fallback-pos: 30% 20%; --fallback-opacity: 30%">
        <span class="font-display text-5xl tracking-wide text-paper/72">SK DUO</span>
      </div>
    @endif

    @if($card['on_sale'])
      <span class="absolute left-3 top-3 rounded-full bg-ink px-3 py-1 text-[0.62rem] font-extrabold uppercase tracking-[0.14em] text-paper md:left-4 md:top-4">
        {{ $card['sale_label'] }}
      </span>
    @endif

    <span class="skduo-product-card__action" aria-hidden="true">
      <span class="truncate">{{ $card['button_text'] }}</span>
      <span class="skduo-product-card__action-icon">
        @if($card['is_external'])
          <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 11L11 5M6 5h5v5"/></svg>
        @else
          <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
        @endif
      </span>
    </span>
  </div>

  <div class="mt-3.5 flex flex-col gap-1 px-0.5 md:mt-4">
    @if($card['category'])
      <p class="text-[0.62rem] font-extrabold uppercase tracking-[0.16em] text-ink/55">{{ $card['category'] }}</p>
    @endif

    <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-0.5">
      <{{ $headingTag }} class="min-w-0 font-display text-[1.35rem] leading-[1.02] tracking-[0.02em] text-ink transition-colors duration-300 group-hover:text-green-deep md:text-[1.6rem]">
        <a href="{{ esc_url($card['url']) }}" @if($card['is_external']) target="_blank" rel="noopener" @endif class="after:absolute after:inset-0 after:content-[''] focus-visible:outline-none">
          {{ $card['title'] }}
          @if($card['is_external'])
            <span class="sr-only">({{ $card['button_text'] }}, avaneb uues aknas)</span>
          @endif
        </a>
      </{{ $headingTag }}>

      @if($card['price_html'])
        <div class="skduo-product-price shrink-0 font-display text-[1.2rem] leading-none tracking-[0.03em] text-green-deep md:text-[1.35rem]">
          {!! wp_kses_post($card['price_html']) !!}
        </div>
      @endif
    </div>
  </div>
</article>
