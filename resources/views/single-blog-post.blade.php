{{--
  Single blog post content template.
--}}
@if(current_user_can('manage_options'))
  <!-- Blade template: resources/views/single-blog-post.blade.php -->
@endif

@while(have_posts())
  @php
    the_post();

    $categories = get_the_category();
    $category = ! empty($categories) ? $categories[0]->name : __('Uudis', 'skduo');
    $postsPageId = (int) get_option('page_for_posts');
    $postsUrl = $postsPageId ? get_permalink($postsPageId) : home_url('/uudised');
    $featuredImage = get_the_post_thumbnail_url(get_the_ID(), 'full');
  @endphp

  <article class="bg-paper text-ink">
    <header class="relative overflow-hidden bg-(--skduo-section-dark) px-4 py-14 text-paper sm:px-6 md:px-12 md:py-24">
      @if($featuredImage)
        <div class="absolute inset-0 z-0">
          <img src="{{ esc_url($featuredImage) }}" alt="" class="h-full w-full object-cover" loading="eager">
        </div>
        <div class="absolute inset-0 z-[1] bg-(--skduo-section-dark)/95"></div>
      @endif

      <div class="pointer-events-none absolute inset-x-0 bottom-0 z-[2] h-1/2 bg-linear-to-t from-paper to-transparent"></div>

      <div class="relative z-10 mx-auto max-w-5xl text-center">
        <a href="{{ esc_url($postsUrl) }}" class="mb-10 inline-flex items-center justify-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint transition hover:text-lime">
          <span aria-hidden="true">←</span>
          Uudised
        </a>

        <div class="mb-5 flex flex-wrap items-center justify-center gap-4 text-xs font-extrabold uppercase tracking-[0.18em] text-paper/55">
          <span class="text-green-mint">{{ $category }}</span>
          <time datetime="{{ esc_attr(get_the_date('c')) }}">{{ get_the_date('d.m.Y') }}</time>
        </div>

        <h1 class="mx-auto max-w-4xl font-serif-display text-[clamp(2.8rem,15vw,7rem)] leading-[0.92] tracking-normal">
          {!! get_the_title() !!}
        </h1>

        @if(has_excerpt())
          <p class="mx-auto mt-7 max-w-2xl text-base leading-relaxed text-paper/72 md:text-lg">
            {!! get_the_excerpt() !!}
          </p>
        @endif
      </div>
    </header>

    <div class="px-4 py-12 sm:px-6 md:px-12 md:py-20">
      <div class="mx-auto mb-12 flex max-w-4xl flex-wrap items-center justify-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-ink/45">
        <time datetime="{{ esc_attr(get_the_date('c')) }}">{{ get_the_date('d.m.Y') }}</time>
        <span class="h-1 w-1 rounded-full bg-green-deep"></span>
        <span class="text-green-deep">{{ $category }}</span>
      </div>

      <div class="skduo-article-content mx-auto max-w-4xl">
        @php
          the_content();
        @endphp
      </div>

      @php
        $previousPost = get_previous_post();
        $nextPost = get_next_post();
      @endphp

      @if($previousPost || $nextPost)
        <nav class="mx-auto mt-16 flex max-w-4xl flex-col gap-5 border-t border-ink/10 pt-8 sm:flex-row sm:items-start sm:justify-between" aria-label="{{ esc_attr__('Postituste navigeerimine', 'skduo') }}">
          @if($previousPost)
            <a href="{{ esc_url(get_permalink($previousPost)) }}" class="group block max-w-sm text-left">
              <span class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-ink/45 transition group-hover:text-green-deep">
                <span aria-hidden="true">←</span>
                Eelmine
              </span>
              <span class="mt-2 block font-serif-display text-3xl leading-none text-ink transition group-hover:text-green-deep">{{ get_the_title($previousPost) }}</span>
            </a>
          @endif

          @if($nextPost)
            <a href="{{ esc_url(get_permalink($nextPost)) }}" class="group block max-w-sm text-left sm:ml-auto sm:text-right">
              <span class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-ink/45 transition group-hover:text-green-deep sm:justify-end">
                Järgmine
                <span aria-hidden="true">→</span>
              </span>
              <span class="mt-2 block font-serif-display text-3xl leading-none text-ink transition group-hover:text-green-deep">{{ get_the_title($nextPost) }}</span>
            </a>
          @endif
        </nav>
      @endif
    </div>
  </article>
@endwhile
