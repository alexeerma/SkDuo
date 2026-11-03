{{--
  Template Name: Klubist
  Club information and timeline page for /klubist.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/page-klubist.blade.php -->
  @endif

  @php
    $pageOptions = function_exists('skduo_get_page_copy_options') ? skduo_get_page_copy_options('klubist') : [];
    $heroLines = array_values(array_filter(array_map('trim', preg_split('/\R+/', (string) ($pageOptions['hero_title'] ?? '')) ?: [])));
    $timelineBlocks = array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', (string) ($pageOptions['timeline_items'] ?? '')) ?: [])));
    $timelineItems = array_values(array_filter(array_map(function ($block) {
      $lines = array_values(array_filter(array_map('trim', preg_split('/\R+/', $block) ?: [])));

      if (empty($lines)) {
        return null;
      }

      $heading = array_shift($lines);
      $parts = array_map('trim', explode('|', $heading, 2));

      return [
        'year' => $parts[0] ?? '',
        'title' => $parts[1] ?? ($parts[0] ?? ''),
        'text' => implode("\n", $lines),
      ];
    }, $timelineBlocks)));
    $timelineImages = function_exists('skduo_get_klubist_timeline_images') ? skduo_get_klubist_timeline_images() : [];
  @endphp

  <section class="relative overflow-hidden bg-[var(--skduo-hero-bg)] px-4 py-16 text-paper sm:px-6 md:px-12 md:py-28">
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-paper to-transparent"></div>

    <div class="relative z-10 mx-auto max-w-[1360px]">
      <span class="mb-7 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.2em] text-green-mint">
        <span class="h-px w-8 bg-green-mint"></span>
        {{ $pageOptions['hero_eyebrow'] ?? 'SK DUO' }}
      </span>

      <h1 class="font-serif-display max-w-5xl text-[clamp(3rem,16vw,8rem)] leading-[0.9] tracking-normal">
        @foreach($heroLines as $line)
          @if($loop->last)
            <em class="text-green-mint">{{ $line }}</em>
          @else
            {{ $line }}
          @endif
          @if(! $loop->last)<br>@endif
        @endforeach
      </h1>

      <p class="mt-7 max-w-2xl text-base leading-relaxed text-paper/72 md:text-lg">
        {{ $pageOptions['hero_text'] ?? '' }}
      </p>
    </div>
  </section>

  <section class="bg-paper px-4 py-14 text-ink sm:px-6 md:px-12 md:py-20">
    <div class="mx-auto max-w-[1360px]">
      @php($hasEditorContent = false)
      @while(have_posts())
        @php(the_post())
        @if(trim(strip_tags(get_the_content())) !== '')
          @php($hasEditorContent = true)
          <div class="skduo-article-content max-w-4xl">
            @php(the_content())
          </div>
        @endif
      @endwhile

      <div class="@if($hasEditorContent) mt-16 @endif grid min-w-0 gap-10 lg:grid-cols-[minmax(0,0.38fr)_minmax(0,0.62fr)] lg:items-start">
        <div>
          <span class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
            <span class="h-px w-8 bg-green-deep"></span>
            {{ $pageOptions['timeline_eyebrow'] ?? 'Meie teekond' }}
          </span>
          <h2 class="font-serif-display text-4xl leading-none tracking-normal md:text-6xl">
            {{ $pageOptions['timeline_title'] ?? 'Mida oleme teinud ja võitnud' }}
          </h2>
          <p class="mt-5 max-w-xl text-base leading-relaxed text-ink/62">
            {{ $pageOptions['timeline_intro'] ?? '' }}
          </p>
        </div>

        <div>
          <div class="relative grid gap-0">
            @foreach($timelineItems as $item)
              <article
                class="skduo-timeline-item grid grid-cols-[2.75rem_minmax(0,1fr)] gap-4 sm:grid-cols-[7rem_2.75rem_minmax(0,1fr)] sm:gap-5"
                data-reveal
                style="--timeline-delay: {{ min($loop->index * 90, 360) }}ms;"
              >
                <div class="hidden pt-1 text-right sm:block">
                  <div class="skduo-timeline-year text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">{{ $item['year'] }}</div>
                </div>

                <div class="relative flex justify-center">
                  @if(! $loop->last)
                    <div class="skduo-timeline-line absolute bottom-0 top-10 w-px bg-ink/10"></div>
                  @endif
                  <div class="skduo-timeline-node relative z-10 grid h-10 w-10 place-items-center rounded-full border border-green-deep/30 bg-paper">
                    <span class="h-3 w-3 rounded-full bg-green-deep shadow-[0_0_0_6px_color-mix(in_srgb,var(--color-green-mint)_16%,transparent)]"></span>
                  </div>
                </div>

                <div class="pb-8">
                  <div class="skduo-timeline-card border border-ink/10 bg-white p-5 sm:p-6">
                    <div class="mb-3 inline-flex border border-green-deep/20 bg-green-mint/10 px-3 py-1 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep sm:hidden">
                      {{ $item['year'] }}
                    </div>
                    <h3 class="font-serif-display text-3xl leading-none tracking-normal text-ink [overflow-wrap:anywhere] md:text-4xl">
                      {{ $item['title'] }}
                    </h3>
                    @if(! empty($item['text']))
                      <div class="mt-4 space-y-3 text-sm leading-relaxed text-ink/64 md:text-base">
                        @foreach(array_filter(array_map('trim', explode("\n", $item['text']))) as $paragraph)
                          <p>{{ $paragraph }}</p>
                        @endforeach
                      </div>
                    @endif
                    @if(! empty($timelineImages[$loop->index]))
                      <img
                        src="{{ esc_url($timelineImages[$loop->index]) }}"
                        alt=""
                        loading="lazy"
                        class="mt-5 aspect-[16/10] w-full object-cover"
                      >
                    @endif
                  </div>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
