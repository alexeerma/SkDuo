{{--
  Template Name: Tule toetajaks
  Partner inquiry page with an editable process timeline.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/page-tule-toetajaks.blade.php -->
  @endif

  @php
    $pageOptions = function_exists('skduo_get_page_copy_options') ? skduo_get_page_copy_options('tule_toetajaks') : [];
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
    $primaryUrl = trim((string) ($pageOptions['cta_primary_url'] ?? 'mailto:info@skduo.ee'));
    $secondaryUrl = trim((string) ($pageOptions['cta_secondary_url'] ?? '/toetajad'));
    $homeHost = parse_url(home_url('/'), PHP_URL_HOST);
    $primaryHost = parse_url($primaryUrl, PHP_URL_HOST);
    $secondaryHost = parse_url($secondaryUrl, PHP_URL_HOST);
  @endphp

  <section class="bg-paper px-4 py-14 text-ink sm:px-6 md:px-12 md:py-20">
    {{-- The visible hero headline sits in the closing CTA; this keeps the page title as the first heading. --}}
    <h1 class="sr-only">{{ get_the_title() }}</h1>
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

      @if(! empty($timelineItems))
        <div class="@if($hasEditorContent) mt-16 border-t border-ink/10 pt-14 md:pt-20 @endif grid gap-10 lg:grid-cols-[0.38fr_0.62fr] lg:items-start">
          <div data-reveal>
            <span class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
              <span class="h-px w-8 bg-green-deep"></span>
              {{ $pageOptions['timeline_eyebrow'] ?? 'Tule toetajaks' }}
            </span>
            <h2 class="font-serif-display text-4xl leading-none tracking-normal md:text-6xl">
              {{ $pageOptions['timeline_title'] ?? 'Kuidas koostöö algab' }}
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
                      <h3 class="font-serif-display text-3xl leading-none tracking-normal text-ink md:text-4xl">
                        {{ $item['title'] }}
                      </h3>
                      @if(! empty($item['text']))
                        <div class="mt-4 space-y-3 text-sm leading-relaxed text-ink/64 md:text-base">
                          @foreach(array_filter(array_map('trim', explode("\n", $item['text']))) as $paragraph)
                            <p>{{ $paragraph }}</p>
                          @endforeach
                        </div>
                      @endif
                    </div>
                  </div>
                </article>
              @endforeach
            </div>
          </div>
        </div>
      @endif
    </div>
  </section>

  <section class="relative overflow-hidden bg-hero-dark text-paper">
    <div class="relative z-10 px-4 py-16 text-center sm:px-6 md:px-12 md:py-28">
      <div data-reveal>
        <span class="mb-5 inline-flex items-center gap-2 text-[0.65rem] font-extrabold uppercase tracking-[0.22em] text-green-mint">
          <span class="h-px w-7 bg-green-mint"></span>
          {{ $pageOptions['hero_eyebrow'] ?? 'Saa partneriks' }}
          <span class="h-px w-7 bg-green-mint"></span>
        </span>
        <h2 class="font-display text-4xl leading-[0.9] tracking-wide sm:text-5xl md:text-7xl">
          @foreach($heroLines as $line)
            {{ $line }}@if(! $loop->last)<br>@endif
          @endforeach
        </h2>
        <p class="mx-auto mt-7 max-w-lg text-base leading-relaxed text-paper/60">
          {{ $pageOptions['hero_text'] ?? '' }}
        </p>
        <div class="mt-10 flex flex-col justify-center gap-4 sm:flex-row sm:flex-wrap">
          @if($primaryUrl)
            <a href="{{ esc_url($primaryUrl) }}" class="btn-primary" data-magnet="0.3" @if($primaryHost && $primaryHost !== $homeHost) target="_blank" rel="noopener" @endif>{{ $pageOptions['cta_primary_label'] ?? 'Kirjuta meile' }} →</a>
          @endif
          @if($secondaryUrl)
            <a href="{{ esc_url($secondaryUrl) }}" class="inline-flex items-center justify-center gap-2 border border-paper/30 px-7 py-4 text-xs font-extrabold uppercase tracking-widest text-paper transition hover:border-paper hover:bg-paper hover:text-ink" @if($secondaryHost && $secondaryHost !== $homeHost) target="_blank" rel="noopener" @endif>{{ $pageOptions['cta_secondary_label'] ?? 'Vaata toetajaid' }}</a>
          @endif
        </div>
      </div>
    </div>
  </section>
@endsection
