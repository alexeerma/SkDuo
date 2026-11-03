{{--
  Reusable dynamic logo slider.
  Usage in Blade: <x-logo-slider />
  Optional props: <x-logo-slider eyebrow="Partnerid" title="Meie toetajad" theme="dark" />
  Usage in page content: [skduo_logo_slider]
--}}
@props([
  'eyebrow' => __('Partnerid', 'skduo'),
  'title' => __('Meie toetajad', 'skduo'),
  'theme' => 'paper',
])

{!! function_exists('skduo_render_logo_slider') ? skduo_render_logo_slider([
  'eyebrow' => $eyebrow,
  'title' => $title,
  'theme' => $theme,
]) : '' !!}
