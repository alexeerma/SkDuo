{{--
  Training sign-up form modal — shared by the homepage hero and /noored, so both
  "Tule trenni" buttons open the exact same popup. Renders nothing when no form
  embed is configured. Any element with [data-training-modal-open] on the page
  opens it; behavior lives in resources/js/animations.js.

  Used as: <x-training-modal :form-embed="$formEmbed" :title="$popupTitle" />
--}}
@props(['formEmbed', 'title' => null])

@php
  $formEmbed = trim((string) $formEmbed);
@endphp

@if($formEmbed !== '')
  <div
    class="fixed inset-0 z-[90] hidden items-center justify-center bg-ink/72 px-4 py-6 backdrop-blur-sm sm:px-6"
    data-training-modal
    aria-hidden="true"
  >
    <div class="absolute inset-0" data-training-modal-close></div>
    <div
      class="relative z-10 flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden border border-paper/12 bg-paper text-ink shadow-[0_30px_90px_rgba(10,21,24,.45)]"
      role="dialog"
      aria-modal="true"
      aria-labelledby="training-modal-title"
    >
      <div class="flex items-center justify-between gap-4 border-b border-ink/10 px-5 py-4 sm:px-6">
        <h2 id="training-modal-title" class="font-display text-3xl leading-none tracking-wide sm:text-4xl">
          {{ $title ?: 'Liitumisavaldus' }}
        </h2>
        <button
          type="button"
          class="grid h-11 w-11 place-items-center rounded-full border border-ink/15 text-ink transition hover:border-green-deep hover:bg-green-deep hover:text-paper focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-deep"
          data-training-modal-close
          aria-label="Sulge vorm"
        >
          <span class="relative block h-4 w-4" aria-hidden="true">
            <span class="absolute left-0 top-1/2 h-0.5 w-4 -translate-y-1/2 rotate-45 bg-current"></span>
            <span class="absolute left-0 top-1/2 h-0.5 w-4 -translate-y-1/2 -rotate-45 bg-current"></span>
          </span>
        </button>
      </div>
      <div class="min-h-[520px] overflow-auto bg-white p-0 [&_iframe]:block [&_iframe]:min-h-[520px] [&_iframe]:w-full">
        {!! $formEmbed !!}
      </div>
    </div>
  </div>
@endif
