{{--
  Shared shell for WooCommerce account/cart/checkout pages — wider container than the
  generic page template so WooCommerce blocks have room, styled via the .woocommerce
  rules in app.css.

  Used as: @include('partials.woo-page', ['eyebrow' => 'Minu konto', 'maxWidthClass' => 'max-w-[1000px]'])
--}}
@php
  $maxWidthClass = $maxWidthClass ?? 'max-w-[1000px]';
@endphp

@while(have_posts())
  @php the_post(); @endphp

  <article class="px-4 py-14 sm:px-6 md:px-12 md:py-20">
    <div class="mx-auto {{ $maxWidthClass }}">
      <span class="mb-4 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
        <span class="h-0.5 w-8 bg-green-deep"></span>
        {{ $eyebrow }}
      </span>
      <h1 class="mb-10 font-display text-4xl leading-none tracking-wide sm:text-5xl md:text-6xl">
        {!! get_the_title() !!}
      </h1>

      @php the_content(); @endphp
    </div>
  </article>
@endwhile
