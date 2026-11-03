{{--
  WooCommerce My Account page — shell in partials.woo-page.
--}}
@extends('layouts.app')

@section('content')
  @include('partials.woo-page', ['eyebrow' => 'Minu konto', 'maxWidthClass' => 'max-w-[1000px]'])
@endsection
