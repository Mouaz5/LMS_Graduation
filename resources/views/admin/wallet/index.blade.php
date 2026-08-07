<x-layouts.app :pageTitle="__('Wallet')">
    <style>
        .page-header { margin-bottom: 24px; }
        .page-header h2 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .page-header p { font-size: 13px; color: #64748b; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: white; border-radius: 14px; padding: 20px;
            border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .stat-label { font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; color: #0f172a; }
        .stat-card.success .stat-value { color: #059669; }
        .stat-card.pending .stat-value { color: #d97706; }
        .stat-card.failed .stat-value { color: #dc2626; }

        .filter-bar {
            background: white; border-radius: 14px;
            border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 16px 20px; margin-bottom: 20px;
            display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .filter-select, .filter-input {
            padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13px; font-family: 'DM Sans', sans-serif; color: #374151;
            background: #fafafa; outline: none; transition: border 0.2s; min-width: 160px;
        }
        .filter-select:focus, .filter-input:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .btn-filter {
            padding: 8px 18px; background: #4F46E5; color: white; border: none;
            border-radius: 8px; font-size: 13px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
        }
        .btn-filter:hover { background: #4338ca; }
        .btn-clear {
            padding: 8px 14px; font-size: 12.5px; color: #64748b; text-decoration: none; font-weight: 500;
        }

        .table-card {
            background: white; border-radius: 14px;
            border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .table-header { padding: 20px; border-bottom: 1px solid #f1f5f9; }
        .table-title { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #0f172a; }
        .table-meta { font-size: 12px; color: #94a3b8; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th {
            padding: 12px 16px; text-align: start;
            font-size: 11px; font-weight: 700; color: #94a3b8;
            letter-spacing: 0.8px; text-transform: uppercase;
        }
        th:first-child { padding-inline-start: 20px; }
        td {
            padding: 14px 16px; border-bottom: 1px solid #f8fafc;
            font-size: 13.5px; color: #374151; vertical-align: middle;
        }
        td:first-child { padding-inline-start: 20px; }
        tbody tr:hover { background: #fafbff; }
        tbody tr:last-child td { border-bottom: none; }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11.5px; font-weight: 600;
        }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .badge-succeeded { background: #d1fae5; color: #059669; }
        .badge-pending   { background: #fef9c3; color: #92400e; }
        .badge-failed    { background: #fee2e2; color: #dc2626; }
        .badge-refunded  { background: #eef2ff; color: #4F46E5; }

        .empty-state { text-align: center; padding: 56px 20px; }
        .empty-icon {
            width: 56px; height: 56px; background: #f1f5f9; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
        }

        .pagination-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 20px; border-top: 1px solid #f1f5f9;
            font-size: 12px; color: #64748b;
        }

        .wallet-tabs { display: flex; gap: 4px; margin-bottom: 24px; background: white; border-radius: 12px; padding: 6px; border: 1px solid #f1f5f9; width: fit-content; }
        .wallet-tab { padding: 10px 20px; border-radius: 8px; font-size: 13.5px; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #64748b; text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .wallet-tab:hover { background: #f8fafc; color: #0f172a; }
        .wallet-tab.active { background: #4F46E5; color: white; }
        .wallet-tab svg { width: 16px; height: 16px; }
    </style>

    <div class="page-header">
        <h2>{{ __("Payment Wallet") }}</h2>
        <p>{{ __("View all payments made by parents and track collected amounts") }}</p>
    </div>

    <div class="wallet-tabs">
        <a href="{{ route('admin.wallet.index') }}" class="wallet-tab active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            {{ __("Payments") }}
        </a>
        <a href="{{ route('admin.wallet.tuition-fees') }}" class="wallet-tab">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.121 4.884 7.5 4 5 4v13c2.5 0 5.121.884 7 2.253M12 6.253C13.879 4.884 16.5 4 19 4v13c-2.5 0-5.121.884-7 2.253"/></svg>
            {{ __("Tuition Fees") }}
        </a>
        <a href="{{ route('admin.wallet.salaries') }}" class="wallet-tab">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            {{ __("Salary Transfers") }}
        </a>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card success">
            <div class="stat-label">{{ __("Total Collected") }}</div>
            <div class="stat-value">{{ strtoupper($currency) }} {{ number_format((float) $totalCollected, 2) }}</div>
        </div>
        <div class="stat-card pending">
            <div class="stat-label">{{ __("Pending Payments") }}</div>
            <div class="stat-value">{{ strtoupper($currency) }} {{ number_format((float) $totalPending, 2) }}</div>
        </div>
        <div class="stat-card failed">
            <div class="stat-label">{{ __("Failed Payments") }}</div>
            <div class="stat-value">{{ strtoupper($currency) }} {{ number_format((float) $totalFailed, 2) }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.wallet.index') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">{{ __("Search") }}</label>
                <input type="text" name="search" class="filter-input" placeholder="{{ __('Parent or student name...') }}" value="{{ request('search') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">{{ __("Status") }}</label>
                <select name="status" class="filter-select">
                    <option value="">{{ __("All") }}</option>
                    <option value="succeeded" @selected(request('status') === 'succeeded')>{{ __("Succeeded") }}</option>
                    <option value="pending" @selected(request('status') === 'pending')>{{ __("Pending") }}</option>
                    <option value="failed" @selected(request('status') === 'failed')>{{ __("Failed") }}</option>
                    <option value="refunded" @selected(request('status') === 'refunded')>{{ __("Refunded") }}</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">{{ __("From Date") }}</label>
                <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">{{ __("To Date") }}</label>
                <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="btn-filter">{{ __("Filter") }}</button>
            @if(request('search') || request('status') || request('date_from') || request('date_to'))
                <a href="{{ route('admin.wallet.index') }}" class="btn-clear">{{ __("Clear") }}</a>
            @endif
        </div>
    </form>

    {{-- Payments Table --}}
    <div class="table-card">
        <div class="table-header">
            <div class="table-title">{{ __("All Payments") }}</div>
            <div class="table-meta">{{ __(":count payments", ['count' => $payments->total()]) }}</div>
        </div>

        @if($payments->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div style="font-family: 'Playfair Display', serif; font-size: 16px; color: #94a3b8; margin-bottom: 6px;">{{ __("No Payments Found") }}</div>
                <div style="font-size: 13px; color: #cbd5e1;">{{ __("No payments have been made yet.") }}</div>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __("Parent") }}</th>
                            <th>{{ __("Student") }}</th>
                            <th>{{ __("Academic Year") }}</th>
                            <th>{{ __("Amount") }}</th>
                            <th>{{ __("Date") }}</th>
                            <th>{{ __("Status") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">{{ $payment->parent?->name ?? '—' }}</div>
                                    <div style="font-size: 11.5px; color: #94a3b8;">{{ $payment->parent?->email ?? '' }}</div>
                                </td>
                                <td>{{ $payment->student?->name ?? '—' }}</td>
                                <td>{{ $payment->academicYear?->name ?? '—' }}</td>
                                <td style="font-weight: 600;">{{ strtoupper($payment->currency) }} {{ number_format((float) $payment->amount, 2) }}</td>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">{{ $payment->created_at?->format('M j, Y') }}</div>
                                    <div style="font-size: 11.5px; color: #94a3b8;">{{ $payment->created_at?->format('H:i') }}</div>
                                </td>
                                <td><span class="badge badge-{{ $payment->status->value }}">{{ ucfirst($payment->status->value) }}</span></td>
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
