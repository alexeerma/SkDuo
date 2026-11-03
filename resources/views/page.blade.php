{{--
  Default WordPress page template.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/page.blade.php -->
  @endif

  @while(have_posts())
    @php
      the_post();
    @endphp

    <article class="px-4 py-14 sm:px-6 md:px-12 md:py-20">
      <div class="mx-auto max-w-4xl">
        <h1 class="mb-8 font-display text-4xl leading-none tracking-wide sm:text-5xl md:text-7xl">
          {!! get_the_title() !!}
        </h1>

        <div class="prose max-w-none">
          @php
            the_content();
          @endphp
        </div>
      </div>
    </article>
  @endwhile
@endsection
