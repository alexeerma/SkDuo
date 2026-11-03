{{--
  Site footer.
--}}
<footer class="bg-ink px-4 py-12 pb-6 text-paper md:px-8 md:py-14">
  @if(function_exists('skduo_get_logo_slider_items') && ! empty(skduo_get_logo_slider_items()))
    <div class="-mx-4 -mt-12 mb-10 md:-mx-8 md:-mt-14 md:mb-12">
      <x-logo-slider eyebrow="" :title="function_exists('skduo_get_footer_logo_slider_title') ? skduo_get_footer_logo_slider_title() : __('Meie toetajad', 'skduo')" theme="dark" />
    </div>
  @endif

  <div class="grid gap-8 border-b border-paper/10 pb-10 text-center sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr_1fr] lg:text-left">
    <div class="sm:col-span-2 lg:col-span-1">
      <div class="mb-4 -mt-[50px] flex items-start justify-center gap-3 lg:justify-start">
        {!! function_exists('skduo_footer_logo_markup') ? skduo_footer_logo_markup() : skduo_site_logo_markup() !!}
      </div>
      <p class="mx-auto max-w-xs text-sm leading-relaxed text-paper/60 lg:mx-0">
        MTÜ Spordiklubi DUO. 25 aastat tippvõrkpalli Tartu südames.
      </p>
    </div>

    @php
      $footerColumns = function_exists('skduo_get_footer_menu_columns') ? skduo_get_footer_menu_columns() : [];
    @endphp

    @foreach($footerColumns as $column)
      <div>
        <h4 class="font-display text-base tracking-widest mb-4 text-green-mint">{{ $column['title'] }}</h4>
        <ul class="list-none space-y-2">
          @foreach($column['items'] as $item)
            <li>
              <a
                href="{{ esc_url($item['url']) }}"
                class="rounded text-sm text-paper/70 transition-colors hover:text-green-mint focus-visible:text-green-mint focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-mint"
                @if(! empty($item['target'])) target="{{ esc_attr($item['target']) }}" rel="noopener" @endif
              >
                {{ $item['title'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    @endforeach
  </div>

  <div class="flex flex-col items-center gap-4 pt-5 text-center text-xs tracking-wider text-paper/50 sm:flex-row sm:justify-between sm:text-left">
    <span>© {{ date('Y') }} MTÜ Spordiklubi DUO · Kõik õigused kaitstud · Web by <a href="https://alexeerma.ee" class="text-paper/50 underline decoration-paper/30 underline-offset-2 transition-colors hover:text-green-mint focus-visible:text-green-mint focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-mint" target="_blank" rel="noopener">Alexeerma</a></span>
    @php
      $socialLinks = skduo_get_social_links();
    @endphp
    @if(! empty($socialLinks))
      <div class="flex justify-center gap-2">
        @foreach($socialLinks as $platform => $social)
          <a
            href="{{ esc_url($social['url']) }}"
            class="relative grid h-7 w-7 place-items-center rounded-full border border-paper/25 text-paper/75 transition before:absolute before:-inset-2 before:content-[''] hover:border-green-mint hover:bg-green-mint hover:text-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-mint"
            aria-label="{{ esc_attr($social['label']) }}"
            @if($social['url'] !== '#') target="_blank" rel="noopener" @endif
          >
            {!! skduo_social_icon($platform) !!}
          </a>
        @endforeach
      </div>
    @endif
  </div>
</footer>
