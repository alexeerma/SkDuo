{{--
  WooCommerce single product. Data: App\View\Composers\SingleProduct.
  The add-to-cart form is WooCommerce's own (loop + global $product required).
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/single-product.blade.php -->
  @endif

  @while(have_posts())
    @php
      the_post();
      global $product;
      $product = function_exists('wc_get_product') ? wc_get_product(get_the_ID()) : null;
    @endphp

    @if($product && ! empty($productData))
      @php($p = $productData)

      <section class="bg-paper px-4 pb-16 pt-6 text-ink sm:px-6 md:px-12 md:pb-24 md:pt-8">
        <div class="mx-auto max-w-[1360px]">
          <nav aria-label="Leivapuru" class="mb-6 flex flex-wrap items-center gap-x-2 gap-y-1 text-[0.68rem] font-extrabold uppercase tracking-[0.14em] text-ink/62 md:mb-8">
            <a href="{{ esc_url($p->shopUrl) }}" class="rounded-sm transition-colors hover:text-green-deep focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-deep">{{ $p->shopTitle }}</a>
            @if($p->category)
              <span aria-hidden="true">/</span>
              <a href="{{ esc_url($p->category['url']) }}" class="rounded-sm transition-colors hover:text-green-deep focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-deep">{{ $p->category['name'] }}</a>
            @endif
            <span aria-hidden="true">/</span>
            <span class="text-ink/80" aria-current="page">{{ $p->name }}</span>
          </nav>

          <div class="skduo-product-notices">
            @php(do_action('woocommerce_before_single_product'))
          </div>

          <div class="grid gap-8 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,1fr)] lg:gap-14 xl:gap-20">
            {{-- Gallery --}}
            <div class="skduo-product-gallery" data-product-gallery>
              <div class="skduo-product-gallery__stage relative aspect-[4/5] overflow-hidden rounded-[18px] md:rounded-[28px]">
                @if(! empty($p->images))
                  <img
                    src="{{ esc_url($p->images[0]['full']) }}"
                    alt="{{ esc_attr($p->images[0]['alt']) }}"
                    class="absolute inset-0 h-full w-full object-contain p-[8%]"
                    data-product-gallery-main
                    data-default-src="{{ esc_url($p->images[0]['full']) }}"
                    data-default-alt="{{ esc_attr($p->images[0]['alt']) }}"
                    fetchpriority="high"
                  >
                @else
                  <div class="grid h-full w-full place-items-center bg-card-fallback" style="--fallback-pos: 30% 20%; --fallback-opacity: 30%">
                    <span class="font-display text-6xl tracking-wide text-paper/72">SK DUO</span>
                  </div>
                @endif

                @if($p->onSale)
                  <span class="absolute left-4 top-4 rounded-full bg-ink px-3.5 py-1.5 text-[0.65rem] font-extrabold uppercase tracking-[0.14em] text-paper md:left-5 md:top-5">
                    {{ $pageOptions['sale_badge'] ?? 'Soodus' }}
                  </span>
                @endif
              </div>

              @if(count($p->images) > 1)
                <div class="mt-3 flex flex-wrap gap-2 md:mt-4 md:gap-3">
                  @foreach($p->images as $index => $image)
                    <button
                      type="button"
                      class="skduo-product-thumb"
                      aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                      aria-label="Näita pilti {{ $index + 1 }}"
                      data-product-gallery-thumb
                      data-full="{{ esc_url($image['full']) }}"
                      data-alt="{{ esc_attr($image['alt']) }}"
                    >
                      <img src="{{ esc_url($image['thumb']) }}" alt="" class="h-full w-full object-contain p-1.5" loading="lazy">
                    </button>
                  @endforeach
                </div>
              @endif
            </div>

            {{-- Summary --}}
            <div class="lg:sticky lg:top-28 lg:self-start">
              @if($p->category)
                <span class="mb-4 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.2em] text-green-deep">
                  <span class="h-px w-8 bg-green-deep"></span>
                  {{ $p->category['name'] }}
                </span>
              @endif

              <h1 class="font-serif-display text-[clamp(2.5rem,6vw,4.25rem)] leading-[0.95] tracking-normal text-balance">
                {{ $p->name }}
              </h1>

              @if($p->priceHtml)
                <div class="skduo-product-price mt-4 font-display text-[2.25rem] leading-none tracking-[0.03em] text-green-deep md:text-[2.6rem]">
                  {!! wp_kses_post($p->priceHtml) !!}
                </div>
              @endif

              @if($p->shortDescription)
                <div class="skduo-article-content mt-6 max-w-[60ch] text-[0.95rem] leading-relaxed text-ink/72">
                  {!! wp_kses_post(wpautop($p->shortDescription)) !!}
                </div>
              @endif

              <div class="skduo-product-buy mt-7 border-t border-ink/10 pt-7">
                @if($p->isExternal)
                  <a href="{{ esc_url($p->externalUrl) }}" target="_blank" rel="noopener" class="skduo-product-cta">
                    <span>{{ $p->externalText }}<span class="sr-only"> (avaneb uues aknas)</span></span>
                    <span class="skduo-product-cta__icon" aria-hidden="true">
                      <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 11L11 5M6 5h5v5"/></svg>
                    </span>
                  </a>
                  @if(! empty($pageOptions['single_external_note']))
                    <p class="mt-3 text-sm text-ink/62">{{ $pageOptions['single_external_note'] }}</p>
                  @endif
                @else
                  @php(woocommerce_template_single_add_to_cart())
                @endif
              </div>

              @if(! empty($p->infoItems))
                <ul class="mt-7 grid gap-3 rounded-[18px] bg-cream/60 p-5 text-sm">
                  @foreach($p->infoItems as $item)
                    <li class="flex gap-3">
                      <span class="mt-[0.45rem] h-1.5 w-1.5 shrink-0 rounded-full bg-green-deep" aria-hidden="true"></span>
                      <span>
                        <strong class="font-extrabold text-ink">{{ $item['label'] }}</strong>
                        @if($item['text'])
                          <span class="text-ink/68"> {{ $item['text'] }}</span>
                        @endif
                      </span>
                    </li>
                  @endforeach
                </ul>
              @endif

              @if($p->sku || $p->categoryListHtml)
                <dl class="mt-7 grid gap-2 text-xs">
                  @if($p->sku)
                    <div class="flex gap-3">
                      <dt class="w-24 shrink-0 font-extrabold uppercase tracking-[0.14em] text-ink/62">Tootekood</dt>
                      <dd class="font-semibold text-ink/80">{{ $p->sku }}</dd>
                    </div>
                  @endif
                  @if($p->categoryListHtml)
                    <div class="flex gap-3">
                      <dt class="w-24 shrink-0 font-extrabold uppercase tracking-[0.14em] text-ink/62">Kategooria</dt>
                      <dd class="font-semibold text-ink/80 [&_a]:underline [&_a]:decoration-ink/20 [&_a]:underline-offset-2 [&_a:hover]:text-green-deep">{!! wp_kses_post($p->categoryListHtml) !!}</dd>
                    </div>
                  @endif
                </dl>
              @endif
            </div>
          </div>

          @if($p->description || $p->attributesHtml)
            <div class="mt-16 grid gap-10 border-t border-ink/10 pt-10 md:mt-24 lg:grid-cols-2 lg:gap-14 xl:gap-20">
              @if($p->description)
                <div>
                  <h2 class="mb-5 font-display text-2xl leading-none tracking-[0.04em] text-ink md:text-3xl">
                    {{ $pageOptions['single_description_title'] ?? 'Tootest' }}
                  </h2>
                  <div class="skduo-article-content max-w-[68ch]">
                    {!! apply_filters('the_content', $p->description) !!}
                  </div>
                </div>
              @endif

              @if($p->attributesHtml)
                <div>
                  <h2 class="mb-5 font-display text-2xl leading-none tracking-[0.04em] text-ink md:text-3xl">
                    {{ $pageOptions['single_attributes_title'] ?? 'Lisainfo' }}
                  </h2>
                  <div class="skduo-product-attributes">
                    {!! $p->attributesHtml !!}
                  </div>
                </div>
              @endif
            </div>
          @endif

          @if(! empty($p->related))
            <div class="mt-16 border-t border-ink/10 pt-10 md:mt-24">
              <h2 class="mb-7 font-display text-2xl leading-none tracking-[0.04em] text-ink md:mb-9 md:text-3xl">
                {{ $pageOptions['single_related_title'] ?? 'Vaata ka' }}
              </h2>
              <div class="grid grid-cols-2 gap-x-3 gap-y-9 sm:gap-x-5 lg:grid-cols-4">
                @foreach($p->related as $card)
                  @include('partials.product-card', ['card' => $card, 'headingTag' => 'h3'])
                @endforeach
              </div>
            </div>
          @endif
        </div>
      </section>

      @php(do_action('woocommerce_after_single_product'))
    @endif
  @endwhile
@endsection
