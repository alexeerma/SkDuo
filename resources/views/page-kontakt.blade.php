{{--
  Contact page (/kontakt) — dark hero with quick contact actions, then a two-column
  section: page content (address/club/manager info) next to a Contact Form 7 form.
  Copy + the CF7 shortcode are editable at Page options → Kontakt.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/page-kontakt.blade.php -->
  @endif

  @php
    $pageOptions = function_exists('skduo_get_page_copy_options') ? skduo_get_page_copy_options('kontakt') : [];
    $heroLines = array_values(array_filter(array_map('trim', preg_split('/\R+/', (string) ($pageOptions['hero_title'] ?? '')) ?: [])));
    $contactBar = skduo_get_contact_bar_data();
    $socialLinks = function_exists('skduo_get_social_links') ? skduo_get_social_links() : [];
    $cf7Shortcode = trim((string) ($pageOptions['cf7_shortcode'] ?? ''));
    $cf7Active = class_exists('WPCF7_ContactForm');
  @endphp

  <section class="relative overflow-hidden bg-[var(--skduo-hero-bg)] px-4 py-16 text-paper sm:px-6 md:px-12 md:py-24">
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-paper to-transparent"></div>

    <div class="relative z-10 mx-auto max-w-[1360px]">
      <span class="mb-5 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">
        <span class="h-0.5 w-8 bg-green-mint"></span>
        {{ $pageOptions['hero_eyebrow'] ?? 'Kontakt' }}
      </span>

      <h1 class="font-serif-display max-w-3xl text-[clamp(2.8rem,10vw,5.5rem)] leading-[0.94] tracking-normal">
        @foreach($heroLines as $line)
          @if($loop->last)
            <em class="text-green-mint">{{ $line }}</em>
          @else
            {{ $line }}<br>
          @endif
        @endforeach
      </h1>

      <p class="mt-6 max-w-xl text-base leading-relaxed text-paper/72 md:text-lg">
        {{ $pageOptions['hero_text'] ?? '' }}
      </p>

      <div class="mt-9 flex flex-wrap items-center gap-3">
        @if($contactBar->phone)
          <a href="tel:{{ preg_replace('/\s+/', '', $contactBar->phone) }}" class="inline-flex items-center justify-center gap-3 rounded-full bg-green-mint px-6 py-3.5 text-xs font-extrabold uppercase tracking-widest text-ink shadow-cta-glow transition hover:-translate-y-0.5 hover:bg-lime">
            {{ $contactBar->phone }}
          </a>
        @endif
        @if($contactBar->email)
          <a href="mailto:{{ $contactBar->email }}" class="inline-flex items-center justify-center rounded-full border border-paper/35 px-6 py-3.5 text-xs font-extrabold uppercase tracking-widest text-paper transition hover:border-paper hover:bg-paper hover:text-ink">
            {{ $contactBar->email }}
          </a>
        @endif

        @if(! empty($socialLinks))
          <div class="ml-1 flex gap-2">
            @foreach($socialLinks as $platform => $social)
              <a
                href="{{ esc_url($social['url']) }}"
                class="grid h-11 w-11 place-items-center rounded-full border border-paper/25 text-paper/80 transition hover:border-green-mint hover:bg-green-mint hover:text-ink"
                aria-label="{{ esc_attr($social['label']) }}"
                target="_blank" rel="noopener"
              >
                {!! skduo_social_icon($platform) !!}
              </a>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </section>

  <section id="kontakt-vorm" class="bg-paper px-4 py-14 text-ink sm:px-6 md:px-12 md:py-24">
    <div class="mx-auto grid max-w-[1360px] gap-6 lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)] lg:items-start">
      <div class="min-w-0 border border-ink/10 bg-white p-6 md:p-8">
        <span class="mb-5 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
          <span class="h-0.5 w-8 bg-green-deep"></span>
          {{ $pageOptions['contact_eyebrow'] ?? 'Kontaktid' }}
        </span>

        @while(have_posts())
          @php the_post(); @endphp
          <div class="skduo-article-content">
            @php the_content(); @endphp
          </div>
        @endwhile
      </div>

      <div class="skduo-contact-form min-w-0 border border-ink/10 bg-white p-6 md:p-8">
        <span class="mb-5 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
          <span class="h-0.5 w-8 bg-green-deep"></span>
          {{ $pageOptions['form_eyebrow'] ?? 'Saada sõnum' }}
        </span>

        @if($cf7Shortcode !== '' && $cf7Active)
          {!! do_shortcode($cf7Shortcode) !!}
        @elseif(current_user_can('manage_options'))
          <div class="border border-dashed border-ink/20 p-5 text-sm leading-relaxed text-ink/60">
            @if(! $cf7Active)
              Contact Form 7 pistikprogramm ei ole aktiivne. Paigalda ja aktiveeri see, seejärel lisa vormi shortcode alt <strong>Page options → Kontakt</strong>.
            @else
              Lisa Contact Form 7 vormi shortcode (nt <code>[contact-form-7 id="123" title="Kontakt"]</code>) menüüst <strong>Page options → Kontakt</strong>.
            @endif
          </div>
        @else
          <p class="text-sm leading-relaxed text-ink/60">
            Kirjuta meile otse <a href="mailto:{{ $contactBar->email }}" class="font-semibold text-green-deep hover:text-ink">{{ $contactBar->email }}</a>.
          </p>
        @endif
      </div>
    </div>
  </section>
@endsection
