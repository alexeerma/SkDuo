{{--
  WooCommerce product archive / merch catalog.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/archive-product.blade.php -->
  @endif

  @php
    $pageOptions = function_exists('skduo_get_page_copy_options') ? skduo_get_page_copy_options('epood') : [];
    $shopPageId = function_exists('wc_get_page_id') ? (int) wc_get_page_id('shop') : 0;
    $shopUrl = $shopPageId > 0 ? get_permalink($shopPageId) : get_post_type_archive_link('product');
    $queried = get_queried_object();
    $isProductCategory = function_exists('is_product_category') && is_product_category();
    $archiveTitle = $isProductCategory && isset($queried->name)
      ? $queried->name
      : ($shopPageId > 0 ? get_the_title($shopPageId) : __('Fännipood', 'skduo'));
    $archiveDescription = $isProductCategory && isset($queried->description) && $queried->description
      ? wp_strip_all_tags($queried->description)
      : ($pageOptions['hero_text'] ?? __('SK DUO valitud fännitooted ja klubi merch. Tooted liiguvad ostmiseks edasi partneri e-poodi.', 'skduo'));
    $productCount = function_exists('wp_count_posts') ? (int) (wp_count_posts('product')->publish ?? 0) : 0;
    $categories = get_terms([
      'taxonomy' => 'product_cat',
      'hide_empty' => true,
      'orderby' => 'name',
      'order' => 'ASC',
    ]);
    $currentCategoryId = $isProductCategory && isset($queried->term_id) ? (int) $queried->term_id : 0;
    $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
    $productQueryArgs = [
      'post_type' => 'product',
      'post_status' => 'publish',
      'posts_per_page' => (int) get_option('posts_per_page', 12),
      'paged' => $paged,
      'orderby' => [
        'menu_order' => 'ASC',
        'date' => 'DESC',
      ],
    ];

    if ($currentCategoryId > 0) {
      $productQueryArgs['tax_query'] = [[
        'taxonomy' => 'product_cat',
        'field' => 'term_id',
        'terms' => [$currentCategoryId],
      ]];
    }

    $products = new WP_Query($productQueryArgs);
  @endphp

  <section class="relative overflow-hidden bg-[var(--skduo-hero-bg)] px-4 py-16 text-paper sm:px-6 md:px-12 md:py-28" data-product-archive-hero>
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-paper to-transparent"></div>

    <div class="relative z-10 mx-auto grid max-w-[1360px] gap-10 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-end">
      <div>
        <span class="mb-7 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.2em] text-green-mint">
          <span class="h-px w-8 bg-green-mint"></span>
          {{ $pageOptions['hero_eyebrow'] ?? 'SK DUO merch' }}
        </span>

        <h1 class="font-serif-display max-w-5xl text-[clamp(3rem,16vw,8rem)] leading-[0.9] tracking-normal">
          {{ $archiveTitle }}<br>
          <em class="text-green-mint">{{ $pageOptions['hero_emphasis'] ?? 'otse partnerilt.' }}</em>
        </h1>

        <p class="mt-7 max-w-2xl text-base leading-relaxed text-paper/72 md:text-lg">
          {{ $archiveDescription }}
        </p>
      </div>

      <aside class="border border-white/10 bg-white/8 p-6 backdrop-blur-md">
        <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">{{ $pageOptions['catalog_eyebrow'] ?? 'Kataloog' }}</div>
        <div class="mt-4 font-display text-5xl leading-none sm:text-6xl">{{ $productCount }}</div>
        <p class="mt-2 text-sm font-semibold leading-relaxed text-paper/56">
          {{ $pageOptions['catalog_text'] ?? '' }}
        </p>
      </aside>
    </div>
  </section>

  <section class="bg-paper px-4 py-12 text-ink sm:px-6 md:px-12 md:py-20" data-product-archive>
    <div class="mx-auto max-w-[1360px]">
      @if(! is_wp_error($categories) && ! empty($categories))
        <nav class="mb-10 flex flex-wrap gap-2" aria-label="{{ esc_attr__('Tootekategooriad', 'skduo') }}">
          <a href="{{ esc_url($shopUrl) }}" data-product-archive-link class="rounded-full border px-4 py-2 text-xs font-extrabold uppercase tracking-[0.16em] transition {{ $currentCategoryId === 0 ? 'border-green-deep bg-green-deep text-paper' : 'border-ink/15 text-ink/58 hover:border-green-deep hover:text-green-deep' }}">
            Kõik
          </a>
          @foreach($categories as $category)
            <a href="{{ esc_url(get_term_link($category)) }}" data-product-archive-link class="rounded-full border px-4 py-2 text-xs font-extrabold uppercase tracking-[0.16em] transition {{ $currentCategoryId === (int) $category->term_id ? 'border-green-deep bg-green-deep text-paper' : 'border-ink/15 text-ink/58 hover:border-green-deep hover:text-green-deep' }}">
              {{ $category->name }}
            </a>
          @endforeach
        </nav>
      @endif

      @if($products->have_posts())
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
          @while($products->have_posts())
            @php
              $products->the_post();
              $product = function_exists('wc_get_product') ? wc_get_product(get_the_ID()) : null;
              $image = get_the_post_thumbnail_url(get_the_ID(), 'large');
              $excerpt = wp_trim_words(wp_strip_all_tags(strip_shortcodes(get_the_excerpt() ?: get_the_content(null, false))), 18, '...');
              $isExternal = $product && $product->is_type('external');
              $buttonUrl = $isExternal ? $product->get_product_url() : get_permalink();
              $buttonText = $isExternal
                ? ($product->get_button_text() ?: ($pageOptions['external_button_fallback'] ?? __('Osta partneri e-poest', 'skduo')))
                : ($pageOptions['internal_button_fallback'] ?? __('Vaata toodet', 'skduo'));
              $priceHtml = $product ? $product->get_price_html() : '';
            @endphp

            <article class="group flex min-h-full flex-col overflow-hidden border border-ink/10 bg-white transition duration-300 hover:-translate-y-1 hover:border-green-deep hover:shadow-[0_24px_60px_rgba(10,21,24,.12)]">
              <a href="{{ esc_url($buttonUrl) }}" @if($isExternal) target="_blank" rel="noopener" @endif class="block">
                <div class="relative aspect-[4/3] overflow-hidden bg-cream">
                  @if($image)
                    <img src="{{ esc_url($image) }}" alt="{{ esc_attr(get_the_title()) }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                  @else
                    <div class="grid h-full w-full place-items-center bg-card-fallback" style="--fallback-pos: 30% 20%; --fallback-opacity: 30%">
                      <span class="font-display text-5xl tracking-wide text-paper/72">SK DUO</span>
                    </div>
                  @endif

                  @if($isExternal)
                    <span class="absolute left-4 top-4 bg-green-mint px-3 py-1.5 text-[0.62rem] font-extrabold uppercase tracking-[0.16em] text-ink">
                      {{ $pageOptions['external_badge'] ?? 'Partneri e-pood' }}
                    </span>
                  @endif
                </div>
              </a>

              <div class="flex flex-1 flex-col p-5 sm:p-6 md:p-7">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                  <h2 class="font-serif-display text-3xl leading-none tracking-normal text-ink transition group-hover:text-green-deep sm:text-4xl md:text-5xl">
                    {!! get_the_title() !!}
                  </h2>

                  @if($priceHtml)
                    <div class="shrink-0 font-display text-3xl leading-none text-green-deep sm:text-right">
                      {!! wp_kses_post($priceHtml) !!}
                    </div>
                  @endif
                </div>

                @if($excerpt)
                  <p class="mb-6 text-sm leading-relaxed text-ink/62 md:text-base">
                    {{ $excerpt }}
                  </p>
                @endif

                <div class="mt-auto">
                  <a href="{{ esc_url($buttonUrl) }}" @if($isExternal) target="_blank" rel="noopener" @endif class="inline-flex w-full items-center justify-center gap-3 rounded-full bg-green-deep px-5 py-3.5 text-xs font-extrabold uppercase tracking-[0.16em] text-paper transition hover:bg-ink">
                    {{ $buttonText }}
                    <span aria-hidden="true">→</span>
                  </a>
                </div>
              </div>
            </article>
          @endwhile
        </div>

        <div class="mt-12 border-t border-ink/10 pt-8 text-sm font-extrabold uppercase tracking-[0.14em] text-green-deep" data-product-pagination>
          {!! paginate_links([
            'total' => $products->max_num_pages,
            'current' => $paged,
            'prev_text' => '← Eelmised',
            'next_text' => 'Järgmised →',
          ]) !!}
        </div>
        @php(wp_reset_postdata())
      @else
        <div class="border border-dashed border-ink/20 p-10 text-center">
          <h2 class="font-serif-display text-4xl leading-none tracking-normal text-ink md:text-5xl">
            {{ $pageOptions['empty_title'] ?? 'Tooteid ei ole veel lisatud.' }}
          </h2>
          <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-ink/58">
            {{ $pageOptions['empty_text'] ?? '' }}
          </p>
        </div>
      @endif
    </div>
  </section>
@endsection
