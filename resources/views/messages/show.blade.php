@extends('layouts.app')

@section('title', 'Pesan — TPS Portal')

@section('content')
    <div class="max-w-3xl mx-auto space-y-5">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('messages.index') }}" class="text-slate-500 hover:text-slate-700">Pesan</a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-900 font-semibold">Detail</span>
            </nav>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Pesan</h1>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-sm space-y-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    @include('partials.avatar', [
                        'photo' => $message->sender?->employee?->photo,
                        'name' => $message->sender->display_name ?? $message->sender->username ?? 'SYS',
                        'size' => 'w-11 h-11 text-sm',
                        'rounded' => 'rounded-xl',
                        'bgClass' => 'bg-slate-900',
                    ])
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $message->sender->display_name ?? $message->sender->username ?? 'Sistem' }}</p>
                        <p class="text-[11px] text-slate-400">{{ $message->sender->role_label ?? '' }} &bull; {{ $message->created_at?->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold uppercase">{{ $message->related_type }}</span>
            </div>

            <div class="text-xs text-slate-600 leading-relaxed whitespace-pre-line border-t border-slate-100 pt-4">{{ $message->message }}</div>

            @if ($message->attachment_path)
                <a href="{{ $message->attachment_path }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white text-xs font-bold transition">
                    Buka Lampiran / Laporan
                </a>
            @endif

            <div class="pt-4 border-t border-slate-100 text-[11px] text-slate-400">
                Ditujukan kepada: <strong class="text-slate-600">{{ $message->receiver->display_name ?? $message->receiver->username ?? '-' }}</strong>
            </div>
        </div>

        <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition">
            Kembali ke Inbox
        </a>
    </div>
@endsection
