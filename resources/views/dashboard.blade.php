@extends('layouts.app')

@section('title', 'Welcome To Dashboard TPS Key Person Development Web')

@section('content')
    <!-- 1. Header Welcome Banner Component -->
    @include('partials.header-banner')

    <!-- 2. 6 Feature Cards Grid Component -->
    @include('partials.feature-cards')

    <!-- 3. Bottom Metrics & Action Controls Component -->
    @include('partials.metrics-bar')
@endsection

