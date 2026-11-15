@php
    $chartId = $chartId ?? 'radarStrengthChart';
    $radar = $radar ?? ['labels' => [], 'datasets' => []];

    $palette = [
        ['border' => '#1d63ed', 'bg' => 'rgba(29, 99, 237, 0.15)'],
        ['border' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.15)'],
        ['border' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.15)'],
        ['border' => '#ef4444', 'bg' => 'rgba(239, 68, 68, 0.15)'],
        ['border' => '#8b5cf6', 'bg' => 'rgba(139, 92, 246, 0.15)'],
        ['border' => '#06b6d4', 'bg' => 'rgba(6, 182, 212, 0.15)'],
    ];

    $datasets = [];
    foreach ($radar['datasets'] as $index => $dataset) {
        $color = $palette[$index % count($palette)];
        $datasets[] = [
            'label' => $dataset['label'],
            'data' => $dataset['data'],
            'borderColor' => $color['border'],
            'backgroundColor' => $color['bg'],
            'borderWidth' => 2,
            'pointBackgroundColor' => $color['border'],
            'pointBorderColor' => '#ffffff',
            'pointRadius' => 3,
        ];
    }

    $hasData = count($radar['labels']) > 0 && count($datasets) > 0;
@endphp

@if ($hasData)
    <div class="w-full h-80 md:h-96">
        <canvas id="{{ $chartId }}"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            var canvas = document.getElementById(@json($chartId));
            if (!canvas || typeof Chart === 'undefined') return;

            new Chart(canvas, {
                type: 'radar',
                data: {
                    labels: @json($radar['labels']),
                    datasets: @json($datasets),
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            min: 0,
                            max: 5,
                            ticks: { stepSize: 1, backdropColor: 'transparent', color: '#94a3b8' },
                            grid: { color: '#e2e8f0' },
                            angleLines: { color: '#e2e8f0' },
                            pointLabels: {
                                color: '#475569',
                                font: { size: 10, weight: '600' },
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 } },
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': ' + context.formattedValue + ' / 5';
                                },
                            },
                        },
                    },
                },
            });
        })();
    </script>
@else
    <p class="py-10 text-center text-xs text-slate-400">
        Belum ada data penilaian workshop untuk ditampilkan pada chart spider.
    </p>
@endif
