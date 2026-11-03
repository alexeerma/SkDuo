{{--
  Template Name: Arhiiv
  Photo gallery page with a season picker, assignable via Page Attributes.
--}}
@extends('layouts.app')

@php
  $defaultSeason = $arhiivSeasons[0] ?? null;
@endphp

@section('content')
  <section class="bg-paper border-b border-ink/10">
    <div class="px-4 py-14 sm:px-6 md:px-12 md:py-20">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
          <span class="inline-flex items-center gap-2 text-green-deep text-xs font-extrabold uppercase tracking-widest mb-5">
            <span class="w-8 h-0.5 bg-green-deep"></span>
            Arhiiv
          </span>

          <h1 class="max-w-4xl font-display text-5xl leading-[0.88] tracking-wide sm:text-6xl md:text-7xl">
            {{ get_the_title() }}
          </h1>
        </div>

        @if(count($arhiivSeasons) > 1)
          <div class="skduo-season-picker relative" data-season-picker>
            <button
              type="button"
              class="skduo-season-picker__trigger"
              aria-expanded="false"
              aria-label="Hooaja filter"
              data-season-trigger
            >
              <span data-season-label>{{ $defaultSeason->label }}</span>
              <span class="skduo-season-picker__icon" aria-hidden="true"></span>
            </button>
            <div class="skduo-season-picker__menu" data-season-menu>
              @foreach($arhiivSeasons as $season)
                <button type="button" class="skduo-season-picker__option{{ $season->slug === $defaultSeason->slug ? ' is-active' : '' }}" data-season-option data-season-value="{{ esc_attr($season->slug) }}">
                  {{ $season->label }}
                </button>
              @endforeach
            </div>
          </div>
        @elseif($defaultSeason && $defaultSeason->label !== '')
          <p class="inline-flex items-center rounded-full bg-ink px-4 py-2 text-xs font-extrabold uppercase tracking-widest text-paper">
            {{ $defaultSeason->label }}
          </p>
        @endif
      </div>
    </div>
  </section>

  <section class="px-4 py-12 sm:px-6 md:px-12 md:py-20" data-arhiiv-page>
    @if(empty($arhiivSeasons))
      <div class="border border-ink/10 bg-cream px-6 py-10 md:px-10">
        <div class="max-w-3xl">
          <span class="inline-flex items-center gap-2 text-green-deep text-xs font-extrabold uppercase tracking-widest mb-3">
            <span class="w-7 h-0.5 bg-green-deep"></span>
            Sisu puudub
          </span>
          <h2 class="mb-4 font-display text-4xl leading-none tracking-wide sm:text-5xl">Lisa galerii pildid wp-administ.</h2>
          <p class="text-sm leading-relaxed text-ink/65">
            Ava wp-admin &rarr; Page options &rarr; Arhiivi galerii ja lisa vähemalt üks hooaeg koos piltidega.
          </p>
        </div>
      </div>
    @else
      @foreach($arhiivSeasons as $season)
        <div
          data-arhiiv-gallery
          data-season="{{ esc_attr($season->slug) }}"
          class="{{ $season->slug === $defaultSeason->slug ? '' : 'hidden' }}"
        >
          @if(empty($season->images))
            <div class="border border-dashed border-ink/20 p-6 text-sm font-bold uppercase tracking-widest text-ink/62">
              Sellel hooajal pole veel pilte lisatud.
            </div>
          @else
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 md:gap-4">
              @foreach($season->images as $image)
                <button
                  type="button"
                  data-arhiiv-image
                  data-full="{{ esc_url($image['url']) }}"
                  data-alt="{{ esc_attr($image['alt']) }}"
                  class="group relative aspect-square overflow-hidden rounded-2xl border border-ink/10 bg-cream focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-mint"
                >
                  <img
                    src="{{ esc_url($image['url']) }}"
                    alt="{{ esc_attr($image['alt']) }}"
                    loading="lazy"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                  >
                </button>
              @endforeach
            </div>
          @endif
        </div>
      @endforeach

      <div
        data-arhiiv-lightbox
        class="fixed inset-0 z-50 hidden items-center justify-center bg-ink/92 p-4 sm:p-8"
        role="dialog"
        aria-modal="true"
        aria-hidden="true"
      >
        <button
          type="button"
          data-arhiiv-lightbox-close
          class="absolute right-4 top-4 flex h-11 w-11 items-center justify-center rounded-full border border-paper/30 text-paper transition hover:border-paper hover:bg-paper hover:text-ink sm:right-8 sm:top-8"
          aria-label="Sulge"
        >
          &times;
        </button>
        <img data-arhiiv-lightbox-image src="" alt="" class="max-h-full max-w-full rounded-lg object-contain">
      </div>

      <script>
        (function () {
          var page = document.querySelector('[data-arhiiv-page]');
          if (!page) return;

          var picker = document.querySelector('[data-season-picker]');
          var galleries = Array.prototype.slice.call(page.querySelectorAll('[data-arhiiv-gallery]'));

          if (picker && galleries.length > 1) {
            var trigger = picker.querySelector('[data-season-trigger]');
            var label = picker.querySelector('[data-season-label]');
            var options = Array.prototype.slice.call(picker.querySelectorAll('[data-season-option]'));

            function closePicker() {
              picker.classList.remove('is-open');
              trigger.setAttribute('aria-expanded', 'false');
            }

            function selectSeason(slug) {
              galleries.forEach(function (gallery) {
                gallery.classList.toggle('hidden', gallery.getAttribute('data-season') !== slug);
              });
            }

            trigger.addEventListener('click', function () {
              var isOpen = picker.classList.toggle('is-open');
              trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            options.forEach(function (option) {
              option.addEventListener('click', function () {
                options.forEach(function (item) {
                  item.classList.toggle('is-active', item === option);
                });
                if (label) {
                  label.textContent = option.textContent.trim();
                }
                selectSeason(option.getAttribute('data-season-value'));
                closePicker();
              });
            });

            document.addEventListener('click', function (event) {
              if (!picker.contains(event.target)) {
                closePicker();
              }
            });

            document.addEventListener('keydown', function (event) {
              if (event.key === 'Escape') {
                closePicker();
              }
            });
          }

          var lightbox = document.querySelector('[data-arhiiv-lightbox]');
          if (!lightbox) return;

          var image = lightbox.querySelector('[data-arhiiv-lightbox-image]');
          var closeBtn = lightbox.querySelector('[data-arhiiv-lightbox-close]');

          function open(src, alt) {
            image.src = src;
            image.alt = alt || '';
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            lightbox.setAttribute('aria-hidden', 'false');
          }

          function close() {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            lightbox.setAttribute('aria-hidden', 'true');
            image.src = '';
          }

          page.addEventListener('click', function (event) {
            var trigger = event.target.closest('[data-arhiiv-image]');
            if (!trigger) return;
            open(trigger.dataset.full, trigger.dataset.alt);
          });

          closeBtn.addEventListener('click', close);
          lightbox.addEventListener('click', function (event) {
            if (event.target === lightbox) close();
          });
          document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') close();
          });
        })();
      </script>
    @endif
  </section>
@endsection
