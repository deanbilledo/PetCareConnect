@extends('layouts.app')

@section('content')

    <!-- Most Popular Section -->
    @include('partials.most-popular', ['popularShops' => $popularShops])

    <!-- Services Section -->
    @include('partials.services', ['services' => $services])

    <!-- Veterinaries Section -->
    @include('partials.veterinaryshop')

    <!-- Grooming Section -->
    @include('partials.groomingshop')
@endsection
