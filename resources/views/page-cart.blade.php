{{--
  WooCommerce Cart page — shell in partials.woo-page.
--}}
@extends('layouts.app')

@section('content')
  @include('partials.woo-page', ['eyebrow' => 'Ostukorv', 'maxWidthClass' => 'max-w-[1000px]'])
@endsection
