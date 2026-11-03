{{--
  Match archive template for /kalender when served as the Mängud archive.
--}}
@extends('layouts.app')

@section('content')
  @if(current_user_can('manage_options'))
    <!-- Blade template: resources/views/archive-mang.blade.php -->
  @endif

  @include('partials.calendar-list')
@endsection
