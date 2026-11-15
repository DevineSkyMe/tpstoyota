<!-- Activity Data Table Component (Connected to Database) -->
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
    
    <!-- Table Container -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            
            <!-- Table Header -->
            <thead>
                <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                    <th scope="col" class="py-3 px-4 w-10">
                        <input type="checkbox" class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                    </th>
                    <th scope="col" class="py-3 px-4">Project ID</th>
                    <th scope="col" class="py-3 px-4">Agent / Key Person</th>
                    <th scope="col" class="py-3 px-4">Improvement Title & Category</th>
                    <th scope="col" class="py-3 px-4">Area / Department</th>
                    <th scope="col" class="py-3 px-4 text-right">Target vs Progress</th>
                    <th scope="col" class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>

            <!-- Table Body -->
            <tbody class="divide-y divide-slate-100">

                @if(isset($activities) && $activities->count() > 0)
                    @foreach($activities as $index => $activity)
                        @php
                            $emp = $activity->employee;
                            $code = 'KZ-' . ($activity->planned_start ? $activity->planned_start->format('Y') : date('Y')) . '-' . sprintf('%03d', $activity->id + 88);

                            $level = $emp->current_tps_level ?? 'KP3';
                            $progress = (int) ($activity->progress_pct ?? 0);

                            // Progress Color & Label
                            if ($progress >= 100 || $activity->status === 'completed') {
                                $barColor = 'bg-emerald-500';
                                $badgeBg = 'bg-emerald-100 text-emerald-800';
                                $badgeLabel = 'Completed';
                            } elseif ($progress >= 90) {
                                $barColor = 'bg-emerald-500';
                                $badgeBg = 'bg-emerald-50 text-emerald-600';
                                $badgeLabel = 'On Track';
                            } elseif ($progress >= 75) {
                                $barColor = 'bg-purple-500';
                                $badgeBg = 'bg-purple-50 text-purple-600';
                                $badgeLabel = 'Review';
                            } elseif ($progress >= 50) {
                                $barColor = 'bg-amber-500';
                                $badgeBg = 'bg-amber-50 text-amber-600';
                                $badgeLabel = 'In Progress';
                            } else {
                                $barColor = 'bg-orange-500';
                                $badgeBg = 'bg-orange-50 text-orange-600';
                                $badgeLabel = $activity->status === 'delay' ? 'Delayed' : 'In Progress';
                            }

                            // Level Badge style
                            $levelBadge = match($level) {
                                'KP4' => 'bg-amber-100 text-amber-800',
                                'KP3' => 'bg-blue-100 text-blue-800',
                                default => 'bg-purple-100 text-purple-800',
                            };
                        @endphp

                        <tr class="hover:bg-blue-50/20 transition {{ $index === 0 ? 'bg-blue-50/10' : '' }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" {{ $index === 0 ? 'checked' : '' }} class="w-3.5 h-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-blue-600 whitespace-nowrap">
                                <a href="{{ route('activity.show', $activity->id) }}" class="inline-flex items-center gap-1.5 hover:underline" title="Lihat Detail Activity">
                                    <svg class="w-3.5 h-3.5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect width="7" height="9" x="3" y="3" rx="1"/>
                                        <rect width="7" height="5" x="14" y="3" rx="1"/>
                                    </svg>
                                    <span>{{ $code }}</span>
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    @include('partials.avatar', [
                                        'photo' => $emp?->photo,
                                        'name' => $emp->name ?? 'Key Person',
                                        'size' => 'w-8 h-8 text-xs',
                                        'rounded' => 'rounded-full',
                                        'bgClass' => 'bg-blue-100',
                                        'textClass' => 'text-blue-700',
                                    ])
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-slate-800">{{ $emp->name ?? 'Unnamed Agent' }}</span>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold {{ $levelBadge }}">{{ $level }}</span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 font-mono">NIK: {{ $emp->username ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-slate-800 leading-tight">{{ Str::limit($activity->title, 42) }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> {{ $activity->category ?? 'Kaizen Project' }}
                                    @if($activity->batch)
                                        <span class="text-slate-300">•</span> {{ $activity->batch }}
                                    @endif
                                </p>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <p class="font-semibold text-slate-800 leading-tight">
                                    {{ $emp->plant->name ?? 'Karawang Plant' }}
                                </p>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $emp->department->name ?? 'Manufacturing Shop' }}
                                </p>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex flex-col items-end gap-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 text-xs">{{ $progress }}%</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $badgeBg }}">{{ $badgeLabel }}</span>
                                    </div>
                                    <div class="w-24 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="{{ $barColor }} h-full rounded-full" style="width: {{ $progress }}%;"></div>
                                    </div>
                                    @if (($activity->pending_progress_count ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[9px] font-bold">
                                            {{ $activity->pending_progress_count }} menunggu ACC
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('activity.show', $activity->id) }}" class="p-1 rounded text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition" title="Detail Kaizen">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    @if (($currentUser ?? auth()->user())?->isAdmin())
                                        <a href="{{ route('activity.edit', $activity->id) }}" class="p-1 rounded text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit Rencana">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                        </a>
                                        <form action="{{ route('activity.destroy', $activity->id) }}" method="POST" onsubmit="return confirm('Hapus aktivitas ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">
                            <p class="text-sm font-semibold">Tidak ada aktivitas yang sesuai dengan filter.</p>
                            <a href="{{ url('/activity') }}" class="text-blue-600 text-xs hover:underline mt-1 inline-block">Reset Filter</a>
                        </td>
                    </tr>
                @endif

            </tbody>
        </table>
    </div>

    <!-- Table Footer & Pagination Controls -->
    <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
        
        <!-- Left: Status summary -->
        <div class="flex items-center gap-2">
            @if(isset($activities) && $activities instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <span>Showing <span class="font-bold text-slate-700">{{ $activities->firstItem() ?? 0 }}</span> to <span class="font-bold text-slate-700">{{ $activities->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-700">{{ $activities->total() }}</span> entries</span>
            @else
                <span>Showing <span class="font-bold text-slate-700">1</span> to <span class="font-bold text-slate-700">7</span> of <span class="font-bold text-slate-700">32</span> entries</span>
            @endif
            <span class="text-slate-300">•</span>
            <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 font-semibold text-[11px]">1 item selected</span>
        </div>

        <!-- Right: Rows selector & Pagination buttons -->
        <div class="flex items-center gap-4">
            
            <!-- Rows per page -->
            <form method="GET" action="{{ url('/activity') }}" class="flex items-center gap-1.5">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('department_id')) <input type="hidden" name="department_id" value="{{ request('department_id') }}"> @endif
                @if(request('level')) <input type="hidden" name="level" value="{{ request('level') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                <span>Rows:</span>
                <select name="rows" onchange="this.form.submit()" class="bg-white border border-slate-200/90 rounded-lg px-2 py-1 text-xs font-semibold text-slate-700 focus:outline-none cursor-pointer">
                    <option value="10" {{ request('rows', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('rows') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('rows') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </form>

            <!-- Page Buttons (Standard pagination links or visual numbers) -->
            @if(isset($activities) && $activities instanceof \Illuminate\Pagination\LengthAwarePaginator && $activities->hasPages())
                <nav class="flex items-center gap-1" aria-label="Pagination">
                    {{-- Previous Page Link --}}
                    @if ($activities->onFirstPage())
                        <span class="w-7 h-7 rounded-lg border border-slate-200 bg-white text-slate-300 flex items-center justify-center opacity-50 cursor-not-allowed">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        </span>
                    @else
                        <a href="{{ $activities->previousPageUrl() }}" class="w-7 h-7 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        </a>
                    @endif

                    {{-- Numerical Page Links --}}
                    @foreach ($activities->getUrlRange(1, min(5, $activities->lastPage())) as $page => $url)
                        @if ($page == $activities->currentPage())
                            <span class="w-7 h-7 rounded-lg bg-[#1d63ed] text-white font-bold flex items-center justify-center shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-7 h-7 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-medium flex items-center justify-center transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($activities->hasMorePages())
                        <a href="{{ $activities->nextPageUrl() }}" class="w-7 h-7 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    @else
                        <span class="w-7 h-7 rounded-lg border border-slate-200 bg-white text-slate-300 flex items-center justify-center opacity-50 cursor-not-allowed">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </span>
                    @endif
                </nav>
            @else
                <nav class="flex items-center gap-1" aria-label="Pagination">
                    <span class="w-7 h-7 rounded-lg border border-slate-200 bg-white text-slate-400 flex items-center justify-center opacity-50">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </span>
                    <span class="w-7 h-7 rounded-lg bg-[#1d63ed] text-white font-bold flex items-center justify-center shadow-sm">1</span>
                    <span class="w-7 h-7 rounded-lg border border-slate-200 bg-white text-slate-600 flex items-center justify-center">2</span>
                    <span class="w-7 h-7 rounded-lg border border-slate-200 bg-white text-slate-600 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                </nav>
            @endif

        </div>

    </div>

</div>
