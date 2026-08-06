<x-layouts.app :pageTitle="__('Payment History')">
    <style>
        .page-header { margin-bottom: 24px; }
        .page-header h2 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .page-header p { font-size: 13px; color: #64748b; }

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
        .badge-succeeded { background: #d1fae5; color: #059669; }
        .badge-pending { background: #fef9c3; color: #92400e; }
        .badge-failed { background: #fee2e2; color: #dc2626; }
        .badge-refunded { background: #eef2ff; color: #4F46E5; }

        .empty-state { text-align: center; padding: 56px 20px; }
        .empty-icon { width: 56px; height: 56px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }

        .pagination-row { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b; }

        .back-link { margin-bottom: 16px; }
        .back-link a { font-size: 13px; color: #64748b; text-decoration: none; font-weight: 500; }
        .back-link a:hover { color: #4F46E5; }
    </style>

    <div class="back-link">
        <a href="{{ route('parent.payments.index') }}">&larr; {{ __("Back to Payments") }}</a>
    </div>

    <div class="page-header">
        <h2>{{ __("Payment History") }}</h2>
        <p>{{ __("View all your payment receipts and their statuses") }}</p>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div class="table-title">{{ __("Your Payments") }}</div>
            <div class="table-meta">{{ __(":count payments", ['count' => $payments->total()]) }}</div>
        </div>

        @if($payments->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div style="font-family: 'Playfair Display', serif; font-size: 16px; color: #94a3b8; margin-bottom: 6px;">{{ __("No Payments Yet") }}</div>
                <div style="font-size: 13px; color: #cbd5e1;">{{ __("You haven't made any payments yet.") }}</div>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __("Academic Year") }}</th>
                            <th>{{ __("Student") }}</th>
                            <th>{{ __("Amount") }}</th>
                            <th>{{ __("Date") }}</th>
                            <th>{{ __("Status") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td style="font-weight: 600; color: #0f172a;">{{ $payment->academicYear?->name ?? '—' }}</td>
                                <td>{{ $payment->student?->name ?? '—' }}</td>
                                <td style="font-weight: 600;">{{ strtoupper($payment->currency) }} {{ number_format((float) $payment->amount, 2) }}</td>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">{{ $payment->created_at?->format('M j, Y') }}</div>
                                    <div style="font-size: 11.5px; color: #94a3b8;">{{ $payment->created_at?->format('H:i') }}</div>
                                </td>
                                <td><span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
                <div class="pagination-row">
                    <div>{{ __("Page :current of :last", ['current' => $payments->currentPage(), 'last' => $payments->lastPage()]) }}</div>
                    <div style="display: flex; gap: 6px;">
                        @if($payments->onFirstPage())
                            <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">&larr; {{ __("Prev") }}</span>
                        @else
                            <a href="{{ $payments->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; border: 1px solid #e2e8f0; color: #374151; text-decoration: none; font-size: 12px; font-weight: 600;">&larr; {{ __("Prev") }}</a>
                        @endif
                        @if($payments->hasMorePages())
                            <a href="{{ $payments->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #4F46E5; color: white; text-decoration: none; font-size: 12px; font-weight: 600;">{{ __("Next") }} &rarr;</a>
                        @else
                            <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">{{ __("Next") }} &rarr;</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
