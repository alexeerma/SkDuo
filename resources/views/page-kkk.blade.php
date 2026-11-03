{{--
  Template Name: KKK
  FAQ page for /kkk.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/page-kkk.blade.php -->
  @endif

  @php
    $pageOptions = function_exists('skduo_get_page_copy_options') ? skduo_get_page_copy_options('kkk') : [];
    $heroLines = array_values(array_filter(array_map('trim', preg_split('/\R+/', (string) ($pageOptions['hero_title'] ?? '')) ?: [])));
    $faqBlocks = array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', (string) ($pageOptions['faq_items'] ?? '')) ?: [])));
    $faqItems = array_values(array_filter(array_map(function ($block) {
      $lines = array_values(array_filter(array_map('trim', preg_split('/\R+/', $block) ?: [])));

      if (empty($lines)) {
        return null;
      }

      return [
        'question' => array_shift($lines),
        'answer' => implode("\n", $lines),
      ];
    }, $faqBlocks)));
    $linkifyFaqText = function (string $text): string {
      $escaped = esc_html($text);

      $escaped = preg_replace_callback('/https?:\/\/[^\s<]+/i', function ($matches) {
        $url = esc_url($matches[0]);
        return '<a href="' . $url . '" class="font-bold text-green-deep underline decoration-green-deep/30 underline-offset-4 transition hover:text-ink" target="_blank" rel="noopener">' . esc_html($matches[0]) . '</a>';
      }, $escaped);

      $escaped = preg_replace_callback('/(?<![\w@.-])[\w.+-]+@[\w.-]+\.[A-Za-z]{2,}(?![\w@.-])/', function ($matches) {
        $email = sanitize_email($matches[0]);
        if (! $email) {
          return esc_html($matches[0]);
        }

        return '<a href="mailto:' . esc_attr($email) . '" class="font-bold text-green-deep underline decoration-green-deep/30 underline-offset-4 transition hover:text-ink">' . esc_html($matches[0]) . '</a>';
      }, $escaped);

      $escaped = preg_replace_callback('/(?<![\w])(?:\+372\s*)?\d(?:[\s-]?\d){6,10}(?![\w])/', function ($matches) {
        $phone = preg_replace('/[^\d+]/', '', $matches[0]);
        return '<a href="tel:' . esc_attr($phone) . '" class="font-bold text-green-deep underline decoration-green-deep/30 underline-offset-4 transition hover:text-ink">' . esc_html($matches[0]) . '</a>';
      }, $escaped);

      return $escaped;
    };
  @endphp

  <section class="relative overflow-hidden bg-[var(--skduo-hero-bg)] px-4 py-16 text-paper sm:px-6 md:px-12 md:py-28">
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-paper to-transparent"></div>

    <div class="relative z-10 mx-auto max-w-[1360px]">
      <span class="mb-7 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.2em] text-green-mint">
        <span class="h-px w-8 bg-green-mint"></span>
        {{ $pageOptions['hero_eyebrow'] ?? 'Korduma kippuvad küsimused' }}
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
    <div class="mx-auto grid max-w-[1360px] gap-10 lg:grid-cols-[0.42fr_0.58fr] lg:items-start">
      <div>
        <span class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
          <span class="h-px w-8 bg-green-deep"></span>
          KKK
        </span>
        <h2 class="font-serif-display text-4xl leading-none tracking-normal md:text-6xl">
          {{ $pageOptions['intro_title'] ?? 'Kuidas saame aidata?' }}
        </h2>
        <p class="mt-5 max-w-xl text-base leading-relaxed text-ink/62">
          {{ $pageOptions['intro_text'] ?? '' }}
        </p>
      </div>

      <div class="grid gap-3">
        @foreach($faqItems as $item)
          <details class="group border border-ink/10 bg-white">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-5 px-5 py-5 text-left text-base font-extrabold uppercase tracking-[0.12em] text-ink transition hover:text-green-deep sm:px-6">
              <span>{{ $item['question'] }}</span>
              <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-green-deep/30 bg-green-mint/10 text-green-deep transition group-open:rotate-45" aria-hidden="true">
                <span class="relative h-3 w-3">
                  <span class="absolute left-0 top-1/2 h-0.5 w-3 -translate-y-1/2 bg-current"></span>
                  <span class="absolute left-1/2 top-0 h-3 w-0.5 -translate-x-1/2 bg-current"></span>
                </span>
              </span>
            </summary>
            <div class="border-t border-ink/10 px-5 py-5 text-sm leading-relaxed text-ink/64 sm:px-6 md:text-base">
              @foreach(array_filter(array_map('trim', explode("\n", $item['answer']))) as $paragraph)
                <p class="@if(! $loop->first) mt-3 @endif">{!! $linkifyFaqText($paragraph) !!}</p>
              @endforeach
            </div>
          </details>
        @endforeach
      </div>
    </div>
  </section>
@endsection
