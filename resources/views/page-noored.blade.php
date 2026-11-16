{{--
  Youth training page for /noored.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/page-noored.blade.php -->
  @endif

  @php
    $options = function_exists('skduo_get_noored_options') ? skduo_get_noored_options() : [];
    $navItems = function_exists('skduo_parse_noored_nav_items') ? skduo_parse_noored_nav_items($options['nav_labels'] ?? '') : [];
    $pricingRows = function_exists('skduo_parse_noored_pricing_rows') ? skduo_parse_noored_pricing_rows($options['pricing_rows'] ?? '') : [];
    $membershipSections = function_exists('skduo_parse_noored_rules_sections') ? skduo_parse_noored_rules_sections($options['membership_sections'] ?? '') : [];
    $membershipFooterLines = array_values(array_filter(array_map('trim', preg_split('/\R+/', (string) ($options['membership_footer'] ?? '')) ?: [])));
    $sidebarImages = function_exists('skduo_get_noored_sidebar_images') ? skduo_get_noored_sidebar_images() : [];

    $coachPosts = post_type_exists('treener')
      ? get_posts(['post_type' => 'treener', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC']])
      : [];

    $coaches = array_map(function ($post) {
      $name = get_the_title($post);
      return [
        'id' => $post->ID,
        'name' => $name,
        'role' => (string) get_post_meta($post->ID, '_role', true) ?: 'Noortetreener',
        'phone' => (string) get_post_meta($post->ID, '_phone', true),
        'email' => (string) get_post_meta($post->ID, '_email', true),
        'image' => get_the_post_thumbnail_url($post, 'medium_large') ?: '',
        'initials' => collect(explode(' ', $name))->map(function ($part) {
          return function_exists('mb_substr') ? mb_substr($part, 0, 1) : substr($part, 0, 1);
        })->implode(''),
      ];
    }, $coachPosts);

    if (empty($coaches)) {
      $coaches = [
        ['id' => 0, 'name' => 'Argo Meresaar', 'role' => 'Noortetreener', 'phone' => '', 'email' => '', 'image' => '', 'initials' => 'AM'],
        ['id' => 0, 'name' => 'Hendrik Rikand', 'role' => 'Noortetreener', 'phone' => '', 'email' => '', 'image' => '', 'initials' => 'HR'],
        ['id' => 0, 'name' => 'Jaanika Jakobson', 'role' => 'Noortetöö kontakt', 'phone' => '58055888', 'email' => 'info@skduo.ee', 'image' => '', 'initials' => 'JJ'],
      ];
    }

    $trainingGroups = skduo_get_training_groups();

    if (empty($trainingGroups)) {
      $trainingGroups = [
        [
          'coach' => 'Annabel Huik',
          'groups' => [
            ['title' => 'U-18/U-20 tüdrukud (2007-2009)', 'times' => ['Esmaspäev: 17.00-18.30 A. Le Coq SH II saal', 'Teisipäev: 17.30-19.00 TÜ SH kollane saal', 'Kolmapäev: 19.00-21.00 TÜ SH kollane saal/Hall', 'Neljapäev: 16.30-17.30 ÜKE TÜ SH']],
            ['title' => 'U-14/U-16 tüdrukud (2012-2013)', 'times' => ['Teisipäev: 16.00-17.30 TÜ SH kollane saal', 'Kolmapäev: 16.00-17.30 TÜ SH kollane saal', 'Neljapäev: 19.00-20.00 ÜKE TÜ SH', 'Reede: 15.30-17.30 TÜ SH kollane saal']],
          ],
          'contact' => ['phone' => '59115429', 'phone_href' => 'tel:59115429', 'email' => 'annabelhuik@gmail.com', 'extra' => []],
        ],
      ];
    }

    $heroLines = array_values(array_filter(array_map('trim', preg_split('/\R+/', (string) ($options['hero_title'] ?? '')) ?: [])));
    $heroPrimary = $heroLines[0] ?? 'Tule trenni.';
    $heroEmphasis = $heroLines[1] ?? 'Leia oma võrkpall.';
    $introParagraphs = array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', (string) ($options['intro_text'] ?? '')) ?: [])));
    $billingParagraphs = array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', (string) ($options['billing_text'] ?? '')) ?: [])));
    $paymentParagraphs = array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', (string) ($options['payment_text'] ?? '')) ?: [])));
    $pricingHeadings = array_pad(array_map('trim', explode('|', (string) ($options['pricing_headings'] ?? ''))), 4, '');
    $formEmbed = trim((string) ($options['form_iframe_embed'] ?? ''));
    $hasFormEmbed = $formEmbed !== '';
    $primaryLabel = trim((string) ($options['primary_label'] ?? 'Tule trenni'));
    $primaryLabel = $primaryLabel === 'Täida liitumisavaldus' ? 'Tule trenni' : $primaryLabel;
  @endphp

  <section class="relative overflow-hidden bg-[var(--skduo-hero-bg)] px-4 py-14 text-paper sm:px-6 md:px-10 md:py-24">
    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[var(--skduo-hero-bg)] to-transparent"></div>

    <div class="relative mx-auto grid max-w-[1360px] gap-12 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-end">
      <div>
        <span class="mb-5 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">
          <span class="h-0.5 w-8 bg-green-mint"></span>
          {{ $options['hero_eyebrow'] ?? 'Noored' }}
        </span>
        <h1 class="font-serif-display max-w-5xl text-4xl leading-[0.92] tracking-normal sm:text-5xl md:text-7xl lg:text-8xl">
          {{ $heroPrimary }}<br><em class="text-green-mint">{{ $heroEmphasis }}</em>
        </h1>
        <p class="mt-7 max-w-2xl text-base leading-relaxed text-paper/72 md:text-lg">
          {{ $options['hero_subtitle'] ?? '' }}
        </p>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
          @if($hasFormEmbed)
            <button
              type="button"
              class="inline-flex items-center justify-center gap-3 rounded-full bg-green-mint px-6 py-3.5 text-xs font-extrabold uppercase tracking-widest text-ink shadow-cta-glow transition hover:-translate-y-0.5 hover:bg-lime md:text-sm"
              data-training-modal-open
            >
              {{ $primaryLabel }}
              <span aria-hidden="true">→</span>
            </button>
          @endif
          <a href="#lisainfo" class="inline-flex items-center justify-center rounded-full border border-paper/35 px-6 py-3.5 text-xs font-extrabold uppercase tracking-widest text-paper transition hover:border-paper hover:bg-paper hover:text-ink md:text-sm">
            {{ $options['secondary_label'] ?? 'Lisainfo' }}
          </a>
        </div>
      </div>

      <aside class="grid gap-3">
        @foreach($navItems as $item)
          <a href="#{{ $item['target'] }}" class="group flex items-center justify-between border border-white/10 bg-white/8 px-5 py-4 text-xs font-extrabold uppercase tracking-[0.18em] text-paper backdrop-blur-md transition hover:border-green-mint hover:bg-green-mint hover:text-ink">
            {{ $item['label'] }}
            <span class="text-green-mint transition group-hover:text-ink" aria-hidden="true">→</span>
          </a>
        @endforeach
      </aside>
    </div>
  </section>

  <x-training-modal :form-embed="$formEmbed" :title="$options['form_popup_title'] ?? null" />

  <section class="bg-paper px-4 py-14 text-ink sm:px-6 md:px-10 md:py-24">
    <div class="mx-auto grid max-w-[1360px] gap-12 lg:grid-cols-[minmax(0,1fr)_360px]">
      <div class="space-y-7 text-base leading-relaxed text-ink/72">
        <h2 class="font-serif-display text-4xl leading-[0.95] tracking-normal text-ink md:text-6xl">
          {{ $options['intro_title'] ?? '' }}
        </h2>
        @foreach($introParagraphs as $paragraph)
          @if(str_contains($paragraph, 'kolm treeningut') || str_contains($paragraph, '3 trenni'))
            <p class="font-serif-display text-3xl leading-tight text-green-deep md:text-4xl">{{ $paragraph }}</p>
          @else
            <p>{{ $paragraph }}</p>
          @endif
        @endforeach
      </div>

      <div class="lg:self-start">
        <aside id="lisainfo" class="border border-ink/10 bg-white p-6">
          <div class="text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">Lisainfo</div>
          <div class="mt-5 space-y-3 text-sm font-semibold text-ink/68">
            <p>{{ $options['contact_name'] ?? '' }}</p>
            <p><a href="tel:{{ preg_replace('/\s+/', '', $options['contact_phone'] ?? '') }}" class="transition hover:text-green-deep">{{ $options['contact_phone'] ?? '' }}</a></p>
            <p><a href="mailto:{{ esc_attr($options['contact_email'] ?? '') }}" class="transition hover:text-green-deep">{{ $options['contact_email'] ?? '' }}</a></p>
          </div>
        </aside>

        @if(! empty($sidebarImages))
          <div class="mt-6" data-noored-slider>
            <div class="skduo-noored-slider__track" data-noored-slider-track>
              @foreach($sidebarImages as $image)
                <div class="skduo-noored-slider__slide">
                  <img src="{{ $image }}" alt="" loading="lazy">
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </section>

  <section id="treeninggrupid" class="bg-paper px-4 py-14 text-ink sm:px-6 md:px-10 md:py-24">
    <div class="mx-auto max-w-[1360px]">
      <div class="mb-10">
        <span class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
          <span class="h-0.5 w-8 bg-green-deep"></span>
          {{ $options['groups_eyebrow'] ?? 'Treeninggrupid 2025/2026' }}
        </span>
        <h2 class="font-serif-display text-4xl leading-[0.95] tracking-normal md:text-6xl">{{ $options['groups_title'] ?? 'Treenerite kaupa.' }}</h2>
      </div>

      @if(count($trainingGroups) > 6)
        <div class="mb-6">
          <input
            type="search"
            placeholder="Otsi treenerit..."
            class="w-full max-w-sm border border-ink/15 bg-white px-4 py-3 text-sm text-ink transition focus:border-green-deep focus:outline-none sm:max-w-xs"
            data-coach-filter
          >
        </div>
      @endif

      <div class="skduo-coach-grid" data-coach-grid>
        @foreach($trainingGroups as $group)
          @php
            $coachPanelId = 'coach-panel-' . $loop->index;
            $groupCount = count($group['groups']);
            $contact = $group['contact'] ?? [];
            $hasContact = ! empty($contact['phone']) || ! empty($contact['email']) || ! empty($contact['extra']);
          @endphp
          <article class="skduo-coach-card" data-coach-card data-coach-name="{{ esc_attr(mb_strtolower($group['coach'])) }}">
            <div class="min-w-0">
              <h3 class="font-display text-3xl leading-none tracking-wide text-green-deep">{{ $group['coach'] }}</h3>
              <p class="mt-2 text-xs font-extrabold uppercase tracking-[0.14em] text-ink/45">
                {{ $groupCount }} {{ $groupCount === 1 ? 'grupp' : 'gruppi' }}
              </p>
            </div>
            <button
              type="button"
              class="skduo-coach-card__toggle"
              aria-expanded="false"
              aria-controls="{{ $coachPanelId }}"
              data-coach-toggle
            >
              <span class="sr-only">{{ $group['coach'] }}: treeninggrupid</span>
              <span class="skduo-coach-card__label" aria-hidden="true">
                <span class="skduo-coach-card__label-closed">Vaata gruppe</span>
                <span class="skduo-coach-card__label-open">Sulge</span>
              </span>
              <svg class="skduo-coach-card__chevron" viewBox="0 0 12 8" width="12" height="8" fill="none" aria-hidden="true" focusable="false">
                <path d="M1 1.5 6 6.5l5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"/>
              </svg>
            </button>
          </article>

          <div id="{{ $coachPanelId }}" class="skduo-coach-panel" role="region" aria-label="{{ esc_attr($group['coach']) }}: treeninggrupid" data-coach-panel hidden>
            <div class="skduo-coach-panel__inner">
              <div class="skduo-coach-panel__groups">
                @foreach($group['groups'] as $coachGroup)
                  <div>
                    <h4 class="text-sm font-extrabold leading-snug text-ink">{{ $coachGroup['title'] }}</h4>
                    <ul class="mt-2 space-y-1 text-sm leading-relaxed text-ink/65">
                      @foreach($coachGroup['times'] as $time)
                        <li>{{ $time }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endforeach
              </div>

              @if($hasContact)
                <div class="skduo-coach-panel__contact">
                  <p class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.14em] text-green-deep">
                    <span class="h-0.5 w-5 bg-green-deep"></span>
                    Kontakt
                  </p>
                  <ul class="space-y-2 text-sm leading-relaxed">
                    @if(! empty($contact['phone']))
                      <li>
                        <span class="block text-[0.7rem] font-extrabold uppercase tracking-[0.14em] text-ink/45">Telefon</span>
                        <a href="{{ esc_attr($contact['phone_href']) }}" class="skduo-coach-panel__link">{{ $contact['phone'] }}</a>
                      </li>
                    @endif
                    @if(! empty($contact['email']))
                      <li>
                        <span class="block text-[0.7rem] font-extrabold uppercase tracking-[0.14em] text-ink/45">E-post</span>
                        <a href="mailto:{{ esc_attr($contact['email']) }}" class="skduo-coach-panel__link">{{ $contact['email'] }}</a>
                      </li>
                    @endif
                    @foreach($contact['extra'] ?? [] as $line)
                      <li><span class="text-ink/65">{{ $line }}</span></li>
                    @endforeach
                  </ul>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      <p class="mt-4 hidden text-sm font-semibold text-ink/50" data-coach-empty>
        Ühtegi treenerit ei leitud.
      </p>
    </div>
  </section>

  <section id="treenerid" class="bg-[var(--skduo-section-dark)] px-4 py-14 text-paper sm:px-6 md:px-10 md:py-24">
    <div class="mx-auto max-w-[1360px]">
      <div class="mb-10">
        <span class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-mint">
          <span class="h-0.5 w-8 bg-green-mint"></span>
          {{ $options['coaches_eyebrow'] ?? 'Treenerid' }}
        </span>
        <h2 class="font-serif-display text-4xl leading-[0.95] tracking-normal md:text-6xl">{{ $options['coaches_title'] ?? 'Inimesed, kes aitavad mängu armastama hakata.' }}</h2>
      </div>

      <div class="grid grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-5">
        @foreach($coaches as $coach)
          <article class="group border border-white/10 bg-white/8 p-5 transition hover:border-green-mint">
            <div class="grid aspect-[4/5] place-items-center overflow-hidden bg-card-fallback" style="--fallback-pos: 35% 25%; --fallback-opacity: 22%">
              @if(! empty($coach['image']))
                <img src="{{ $coach['image'] }}" alt="{{ $coach['name'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
              @else
                <span class="font-display text-6xl leading-none text-paper/82">{{ $coach['initials'] ?? '' }}</span>
              @endif
            </div>
            <h3 class="mt-4 font-display text-3xl leading-none tracking-wide text-green-mint">{{ $coach['name'] }}</h3>
            <p class="mt-2 text-xs font-extrabold uppercase tracking-[0.18em] text-paper/45">{{ $coach['role'] }}</p>
            @if(! empty($coach['phone']) || ! empty($coach['email']))
              <div class="mt-3 space-y-1 text-sm font-semibold text-paper/68">
                @if(! empty($coach['phone']))
                  <p><a href="tel:{{ preg_replace('/\s+/', '', $coach['phone']) }}" class="transition hover:text-green-mint">{{ $coach['phone'] }}</a></p>
                @endif
                @if(! empty($coach['email']))
                  <p><a href="mailto:{{ esc_attr($coach['email']) }}" class="break-all transition hover:text-green-mint">{{ $coach['email'] }}</a></p>
                @endif
              </div>
            @endif
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section id="hinnakiri" class="bg-paper px-4 py-14 text-ink sm:px-6 md:px-10 md:py-24">
    <div class="mx-auto max-w-[1360px]">
      <div class="mb-10">
        <span class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
          <span class="h-0.5 w-8 bg-green-deep"></span>
          {{ $options['pricing_eyebrow'] ?? 'Hinnakiri' }}
        </span>
        <h2 class="font-serif-display text-4xl leading-[0.95] tracking-normal md:text-6xl">{{ $options['pricing_title'] ?? '' }}</h2>
      </div>

      <div class="overflow-x-auto border border-ink/10 bg-white">
        <table class="w-full min-w-[760px] text-left text-sm">
          <thead class="bg-ink text-paper">
            <tr>
              <th class="px-5 py-4 font-extrabold">{{ $pricingHeadings[0] ?: 'Võrkpall' }}</th>
              <th class="px-5 py-4 font-extrabold">{{ $pricingHeadings[1] ?: '3-5x nädalas' }}</th>
              <th class="px-5 py-4 font-extrabold">{{ $pricingHeadings[2] ?: 'Suvi (juuli-august)' }}</th>
              <th class="px-5 py-4 font-extrabold">{{ $pricingHeadings[3] ?: '2016-2020 grupp' }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pricingRows as $row)
              <tr class="border-t border-dashed border-green-deep/30">
                <td class="px-5 py-5 font-semibold text-ink/72">{{ $row['label'] }}</td>
                <td class="px-5 py-5 font-display text-3xl leading-none">{{ $row['main'] }}</td>
                <td class="px-5 py-5 font-display text-3xl leading-none">{{ $row['summer'] }}</td>
                <td class="px-5 py-5 font-display text-3xl leading-none">{{ $row['young'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-10 grid gap-5 lg:grid-cols-[0.85fr_1.15fr]">
        <div class="border border-ink/10 bg-white p-6">
          <h3 class="font-display text-4xl leading-none text-green-deep">{{ $options['billing_title'] ?? '' }}</h3>
          @foreach($billingParagraphs as $paragraph)
            <p class="mt-4 text-sm leading-relaxed text-ink/68">{{ $paragraph }}</p>
          @endforeach
        </div>
        <div class="border border-ink/10 bg-white p-6">
          <h3 class="font-display text-4xl leading-none text-green-deep">{{ $options['payment_title'] ?? '' }}</h3>
          @foreach($paymentParagraphs as $paragraph)
            <p class="mt-4 text-sm leading-relaxed text-ink/68">{{ $paragraph }}</p>
          @endforeach
        </div>
      </div>

      @if(! empty($membershipSections))
        <div class="mt-16">
          <div class="mb-10">
            <span class="mb-3 inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.18em] text-green-deep">
              <span class="h-0.5 w-8 bg-green-deep"></span>
              {{ $options['membership_eyebrow'] ?? 'Liikmelisus' }}
            </span>
            <h2 class="font-serif-display text-4xl leading-[0.95] tracking-normal md:text-6xl">{{ $options['membership_title'] ?? '' }}</h2>
          </div>

          <div class="grid gap-4 lg:grid-cols-2">
            @foreach($membershipSections as $section)
              <article class="border border-ink/10 bg-white p-6">
                <h3 class="font-display text-3xl leading-none tracking-wide text-green-deep">{{ $section['title'] }}</h3>
                <ul class="mt-5 list-disc space-y-2 pl-5 text-sm leading-relaxed text-ink/68">
                  @foreach($section['items'] as $item)
                    <li>{{ $item }}</li>
                  @endforeach
                </ul>
              </article>
            @endforeach
          </div>

          @if(! empty($membershipFooterLines))
            <div class="mt-8 border-t border-ink/10 pt-6 text-sm leading-relaxed text-ink/58">
              @foreach($membershipFooterLines as $line)
                <p>{{ $line }}</p>
              @endforeach
            </div>
          @endif
        </div>
      @endif
    </div>
  </section>

@endsection
