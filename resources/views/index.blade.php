{{--
  Blog archive and fallback index template.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/index.blade.php -->
  @endif

  @php
    $pageOptions = function_exists('skduo_get_page_copy_options') ? skduo_get_page_copy_options('uudised') : [];
    $isNewsView = is_home() || is_archive();
    $postsPageId = (int) get_option('page_for_posts');
    $postsUrl = $postsPageId ? get_permalink($postsPageId) : home_url('/uudised');
    $categories = get_categories([
      'hide_empty' => true,
      'orderby' => 'name',
      'order' => 'ASC',
    ]);
    $currentCategoryId = is_category() ? (int) get_queried_object_id() : 0;
    $archiveTitle = is_category()
      ? single_cat_title('', false)
      : (is_archive() ? wp_strip_all_tags(get_the_archive_title()) : __('Uudised', 'skduo'));
  @endphp

  <section class="relative overflow-hidden bg-(--skduo-section-dark) px-4 py-16 text-paper sm:px-6 md:px-12 md:py-28">
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-linear-to-t from-paper to-transparent"></div>

    <div class="relative z-10 mx-auto max-w-5xl text-center">
      @if(is_category())
        <a href="{{ esc_url($postsUrl) }}" class="mb-10 inline-flex items-center justify-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint transition hover:text-lime">
          <span aria-hidden="true">←</span>
          {{ $pageOptions['hero_eyebrow'] ?? 'Uudised' }}
        </a>
      @else
        <div class="mb-10 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">
          {{ $pageOptions['hero_eyebrow'] ?? 'Uudised' }}
        </div>
      @endif

      <div class="mb-5 flex flex-wrap items-center justify-center gap-4 text-xs font-extrabold uppercase tracking-[0.18em] text-paper/55">
        <span class="text-green-mint">{{ $pageOptions['hero_meta_label'] ?? 'Klubi uudised' }}</span>
        <span>{{ wp_count_posts('post')->publish }} {{ $pageOptions['post_count_label'] ?? 'postitust' }}</span>
      </div>

      <h1 class="mx-auto max-w-4xl font-serif-display text-[clamp(3rem,16vw,8rem)] leading-[0.9] tracking-normal">
        {{ $archiveTitle }}
      </h1>

      <p class="mx-auto mt-7 max-w-2xl text-base leading-relaxed text-paper/72 md:text-lg">
        {{ $pageOptions['hero_text'] ?? '' }}
      </p>

      @if(! empty($categories))
        <div class="mx-auto mt-10 flex max-w-4xl flex-wrap justify-center gap-2">
          <a href="{{ esc_url($postsUrl) }}" class="rounded-full border px-4 py-2 text-xs font-extrabold uppercase tracking-[0.16em] transition {{ $currentCategoryId === 0 ? 'border-green-mint bg-green-mint text-ink' : 'border-paper/25 text-paper/70 hover:border-green-mint hover:text-green-mint' }}">
            Kõik
          </a>
          @foreach($categories as $cat)
            <a href="{{ esc_url(get_category_link($cat)) }}" class="rounded-full border px-4 py-2 text-xs font-extrabold uppercase tracking-[0.16em] transition {{ $currentCategoryId === (int) $cat->term_id ? 'border-green-mint bg-green-mint text-ink' : 'border-paper/25 text-paper/70 hover:border-green-mint hover:text-green-mint' }}">
              {{ $cat->name }}
            </a>
          @endforeach
        </div>
      @endif
    </div>
  </section>

  <section class="px-4 py-12 sm:px-6 md:px-12 md:py-20">
    <div class="mx-auto max-w-7xl">
      @if(have_posts())
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
          @while(have_posts())
            @php
              the_post();

              $postCategories = get_the_category();
              $category = ! empty($postCategories) ? $postCategories[0]->name : __('Uudis', 'skduo');
              $image = get_the_post_thumbnail(get_the_ID(), 'large', [
                'alt' => get_the_title(),
                'class' => 'h-full w-full mx-auto object-cover opacity-82 transition duration-500 group-hover:scale-105',
                'sizes' => '(min-width: 1280px) 400px, (min-width: 768px) 50vw, 100vw',
              ]);
              $excerpt = wp_trim_words(
                wp_strip_all_tags(strip_shortcodes(get_the_excerpt() ?: get_the_content(null, false))),
                20,
                '...'
              );
            @endphp

            <article class="group overflow-hidden rounded-[28px] border border-ink/10 bg-paper transition duration-300 hover:-translate-y-1 hover:border-green-deep hover:shadow-[0_24px_60px_rgba(10,21,24,.12)]">
              <a href="{{ esc_url(get_permalink()) }}" class="block">
                <div class="relative aspect-[4/3] overflow-hidden bg-ink">
                  @if($image)
                    {!! $image !!}
                  @else
                    <div class="h-full w-full bg-card-fallback" style="--fallback-pos: 28% 20%; --fallback-opacity: 24%"></div>
                  @endif
                  <div class="absolute inset-0 bg-linear-to-t from-ink/82 via-ink/16 to-transparent"></div>
                  <div class="absolute bottom-5 left-5 right-5 flex items-center justify-between gap-4 text-xs font-extrabold uppercase tracking-[0.16em] text-paper/78">
                    <span class="text-green-mint">{{ $category }}</span>
                    <time datetime="{{ esc_attr(get_the_date('c')) }}">{{ get_the_date('d.m.Y') }}</time>
                  </div>
                </div>

                <div class="p-5 sm:p-6 md:p-7">
                  <h2 class="font-serif-display text-3xl leading-none tracking-normal text-ink transition group-hover:text-green-deep sm:text-4xl md:text-5xl">
                    {!! get_the_title() !!}
                  </h2>
                  <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-ink/62 md:text-base">
                    {{ $excerpt }}
                  </p>
                </div>
              </a>
            </article>
          @endwhile
        </div>

        <div class="skduo-blog-pagination mt-12 border-t border-ink/10 pt-8 text-sm font-extrabold uppercase tracking-[0.14em]">
          {!! get_the_posts_pagination([
            'mid_size' => 1,
            'prev_text' => '← Eelmised',
            'next_text' => 'Järgmised →',
          ]) !!}
        </div>
      @else
        <div class="border border-dashed border-ink/20 p-8 text-center text-ink/55">
          Sisu ei leitud.
        </div>
      @endif
    </div>
  </section>
@endsection
