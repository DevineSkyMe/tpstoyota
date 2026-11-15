@extends('layouts.app')

@section('title', 'Pesan — TPS Portal')

@section('content')
    @php($user = auth()->user())

    <div class="space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-700">Home</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-semibold">Pesan</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Kotak Pesan</h1>
                <p class="text-xs text-slate-500">Notifikasi laporan workshop dan pesan internal antar role.</p>
            </div>

            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-white border border-slate-200/90 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                <span class="font-bold text-slate-800">{{ $unread }} belum dibaca</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Message list -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-2">
                    <a href="{{ route('messages.index', ['box' => 'inbox']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $box === 'inbox' ? 'bg-[#1d63ed] text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">Inbox</a>
                    <a href="{{ route('messages.index', ['box' => 'sent']) }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $box === 'sent' ? 'bg-[#1d63ed] text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">Terkirim</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($messages as $message)
                        @php($other = $box === 'inbox' ? $message->sender : $message->receiver)
                        <a href="{{ route('messages.show', $message) }}" class="block px-4 py-3.5 hover:bg-blue-50/20 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $other->display_name ?? $other->username ?? 'Sistem' }}</p>
                                        <span class="text-[10px] uppercase font-semibold text-slate-400">{{ $other->role_label ?? '' }}</span>
                                        @if ($box === 'inbox' && ! $message->is_read)
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $message->message }}</p>
                                </div>
                                <span class="text-[10px] text-slate-400 shrink-0">{{ $message->created_at?->diffForHumans() }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="px-4 py-12 text-center text-xs text-slate-400">Tidak ada pesan pada kotak ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- Compose -->
            <div class="space-y-5">
                @if ($recipients->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-3">
                        <h2 class="text-sm font-bold text-slate-900">Tulis Pesan</h2>
                        <form method="POST" action="{{ route('messages.store') }}" class="space-y-2.5">
                            @csrf
                            <select name="receiver_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs">
                                <option value="">Pilih penerima</option>
                                @foreach ($recipients as $recipient)
                                    <option value="{{ $recipient->id }}">{{ $recipient->display_name }} — {{ $recipient->role_label }}</option>
                                @endforeach
                            </select>
                            <textarea name="message" rows="4" required maxlength="2000" placeholder="Tulis pesan atau catatan tindak lanjut..."
                                      class="w-full p-3 bg-slate-50 border border-slate-200/90 rounded-xl text-xs leading-relaxed"></textarea>
                            <button type="submit" class="w-full px-3 py-2 rounded-xl bg-[#1d63ed] hover:bg-blue-700 text-white text-xs font-bold transition">Kirim</button>
                        </form>
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm text-xs text-slate-500">
                        Anda menerima pesan dari Admin / HRD / OMDD. Fitur kirim pesan hanya tersedia untuk role tersebut.
                    </div>
                @endif

                <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm text-xs space-y-2">
                    <h2 class="text-sm font-bold text-slate-900">Tentang Laporan</h2>
                    <p class="text-slate-500">Laporan hasil workshop yang dikirim Admin akan muncul di inbox Anda beserta tautan laporan.</p>
                </div>
            </div>
        </div>

        <div>{{ $messages->links() }}</div>
    </div>
@endsection
