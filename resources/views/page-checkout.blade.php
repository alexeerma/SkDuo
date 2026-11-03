{{--
  WooCommerce Checkout page — shell in partials.woo-page. Wider max-width than
  My Account/Cart: checkout renders two columns (fields + order summary) side by side.
--}}
@extends('layouts.app')

@section('content')
  @include('partials.woo-page', ['eyebrow' => 'Vormista tellimus', 'maxWidthClass' => 'max-w-[1200px]'])
@endsection
