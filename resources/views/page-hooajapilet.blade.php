{{--
  Template Name: Hooajapilet
  Season ticket landing page for /hooajapilet.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/page-hooajapilet.blade.php -->
  @endif

  @php
    $pageOptions = function_exists('skduo_get_page_copy_options') ? skduo_get_page_copy_options('hooajapilet') : [];
    $headerCta = function_exists('skduo_get_header_cta') ? skduo_get_header_cta() : ['url' => home_url('/kalender')];
    $heroLines = array_values(array_filter(array_map('trim', preg_split('/\R+/', (string) ($pageOptions['hero_title'] ?? '')) ?: [])));
    $ticketUrl = trim((string) ($pageOptions['cta_url'] ?? ''));
    $ticketUrl = $ticketUrl !== '' ? $ticketUrl : ($headerCta['url'] ?? home_url('/kalender'));
    $ticketHost = parse_url($ticketUrl, PHP_URL_HOST);
    $homeHost = parse_url(home_url('/'), PHP_URL_HOST);
    $ticketIsExternal = $ticketHost && $ticketHost !== $homeHost;
  @endphp

  <section class="relative overflow-hidden bg-[var(--skduo-hero-bg)] px-4 py-16 text-paper sm:px-6 md:px-12 md:py-28">
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-paper to-transparent"></div>

    <div class="relative z-10 mx-auto grid max-w-[1360px] gap-10 lg:grid-cols-[minmax(0,1fr)_390px] lg:items-end">
      <div>
        <span class="mb-7 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.2em] text-green-mint">
          <span class="h-px w-8 bg-green-mint"></span>
          {{ $pageOptions['hero_eyebrow'] ?? 'Bigbank Tartu' }}
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

        <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
          <a
            href="{{ esc_url($ticketUrl) }}"
            class="inline-flex items-center justify-center gap-3 rounded-full bg-green-mint px-7 py-4 text-xs font-extrabold uppercase tracking-widest text-ink shadow-cta-glow transition hover:-translate-y-0.5 hover:bg-lime md:text-sm"
            @if($ticketIsExternal) target="_blank" rel="noopener" @endif
          >
            {{ $pageOptions['cta_label'] ?? 'Osta hooajapilet' }}
          </a>
        </div>
      </div>

      <div class="border border-white/10 bg-white/8 p-6 backdrop-blur-md">
        <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">Hooajapilet</div>
        <h2 class="mt-4 font-serif-display text-4xl leading-none tracking-normal text-paper sm:text-5xl">
          {{ $pageOptions['info_title'] ?? 'Üks pilet. Terve hooaeg.' }}
        </h2>
        <p class="mt-5 text-sm font-semibold leading-relaxed text-paper/62">
          {{ $pageOptions['info_text'] ?? '' }}
        </p>
      </div>
    </div>
  </section>

  <section class="bg-paper px-4 py-14 text-ink sm:px-6 md:px-12 md:py-20">
    <div class="mx-auto max-w-[1360px]">
      <div class="skduo-article-content max-w-4xl">
        @while(have_posts())
          @php(the_post())
          @if(trim(strip_tags(get_the_content())) !== '')
            @php(the_content())
          @else
            <h2>{{ $pageOptions['info_title'] ?? 'Üks pilet. Terve hooaeg.' }}</h2>
            <p>{{ $pageOptions['info_text'] ?? '' }}</p>
          @endif
        @endwhile
      </div>
    </div>
  </section>
@endsection
