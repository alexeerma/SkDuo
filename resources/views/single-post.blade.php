{{--
  WordPress blog post single template.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/single-post.blade.php -->
  @endif

  @include('single-blog-post')
@endsection
