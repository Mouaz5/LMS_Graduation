<x-layouts.app :pageTitle="__('Salaries')">
    <style>
        .page-header { margin-bottom: 24px; }
        .page-header h2 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .page-header p { font-size: 13px; color: #64748b; }

        .stat-card {
            background: white; border-radius: 14px; padding: 20px;
            border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            margin-bottom: 24px; max-width: 280px;
        }
        .stat-label { font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; color: #059669; }

        .table-card { background: white; border-radius: 14px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; }
        .table-header { padding: 20px; border-bottom: 1px solid #f1f5f9; }
        .table-title { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #0f172a; }
        .table-meta { font-size: 12px; color: #94a3b8; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th { padding: 12px 16px; text-align: start; font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: 0.8px; text-transform: uppercase; }
        th:first-child { padding-inline-start: 20px; }
        td { padding: 14px 16px; border-bottom: 1px solid #f8fafc; font-size: 13.5px; color: #374151; vertical-align: middle; }
        td:first-child { padding-inline-start: 20px; }
        tbody tr:hover { background: #fafbff; }
        tbody tr:last-child td { border-bottom: none; }

        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 600; }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .badge-paid { background: #d1fae5; color: #059669; }
        .badge-pending { background: #fef9c3; color: #92400e; }
        .badge-failed { background: #fee2e2; color: #dc2626; }

        .empty-state { text-align: center; padding: 56px 20px; }
        .empty-icon { width: 56px; height: 56px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }

        .pagination-row { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b; }
    </style>

    <div class="page-header">
        <h2>{{ __("Salary History") }}</h2>
        <p>{{ __("View all salary transfers sent to you by the school") }}</p>
    </div>

    <div class="stat-card">
        <div class="stat-label">{{ __("Total Received") }}</div>
        <div class="stat-value">{{ strtoupper($currency) }} {{ number_format((float) $totalPaid, 2) }}</div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div class="table-title">{{ __("Transfer History") }}</div>
            <div class="table-meta">{{ __(":count transfers", ['count' => $transfers->total()]) }}</div>
        </div>

        @if($transfers->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div style="font-family: 'Playfair Display', serif; font-size: 16px; color: #94a3b8; margin-bottom: 6px;">{{ __("No Transfers Yet") }}</div>
                <div style="font-size: 13px; color: #cbd5e1;">{{ __("No salary transfers have been sent to you yet.") }}</div>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __("Date") }}</th>
                            <th>{{ __("Amount") }}</th>
                            <th>{{ __("Description") }}</th>
                            <th>{{ __("Status") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">{{ $transfer->transfer_date?->format('M j, Y') }}</div>
                                </td>
                                <td style="font-weight: 600;">{{ strtoupper($transfer->currency) }} {{ number_format((float) $transfer->amount, 2) }}</td>
                                <td style="color: #64748b;">{{ $transfer->description ?? '—' }}</td>
                                <td><span class="badge badge-{{ $transfer->status->value }}">{{ ucfirst($transfer->status->value) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($transfers->hasPages())
                <div class="pagination-row">
                    <div>{{ __("Page :current of :last", ['current' => $transfers->currentPage(), 'last' => $transfers->lastPage()]) }}</div>
                    <div style="display: flex; gap: 6px;">
                        @if($transfers->onFirstPage())
                            <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">&larr; {{ __("Prev") }}</span>
                        @else
                            <a href="{{ $transfers->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; border: 1px solid #e2e8f0; color: #374151; text-decoration: none; font-size: 12px; font-weight: 600;">&larr; {{ __("Prev") }}</a>
                        @endif
                        @if($transfers->hasMorePages())
                            <a href="{{ $transfers->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #4F46E5; color: white; text-decoration: none; font-size: 12px; font-weight: 600;">{{ __("Next") }} &rarr;</a>
                        @else
                            <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">{{ __("Next") }} &rarr;</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
