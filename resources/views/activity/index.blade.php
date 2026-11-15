@extends('layouts.app')

@section('title', 'Activity Monitoring Log — TPS Portal')

@section('content')
    <div class="space-y-5">
        <!-- 1. Activity Header (Breadcrumbs, Title, Action Buttons) -->
        @include('activity.partials.header')

        <!-- 2. KPI Summary Cards (Total Activities, In Progress, Completed) -->
        @include('activity.partials.kpis')

        <!-- 3. Search & Filters Toolbar -->
        @include('activity.partials.filters')

        <!-- 4. Main Activity Data Table -->
        @include('activity.partials.table')

        <!-- 5. Bottom Milestones & Reference Materials -->
        @include('activity.partials.milestones')
    </div>
@endsection

