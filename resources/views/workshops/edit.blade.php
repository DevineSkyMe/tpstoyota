@extends('layouts.app')

@section('title', 'Edit Workshop — TPS Portal')

@section('content')
    <div class="max-w-3xl mx-auto space-y-5">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('workshops.index') }}" class="text-slate-500 hover:text-slate-700">Workshop</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-900 font-semibold">Edit</span>
            </nav>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Batch Workshop</h1>
            <p class="text-xs text-slate-500">{{ $workshop->title }}</p>
        </div>

        <form method="POST" action="{{ route('workshops.update', $workshop) }}" class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-5">
            @csrf
            @method('PUT')
            @include('workshops._form', ['workshop' => $workshop])
        </form>
    </div>
@endsection
