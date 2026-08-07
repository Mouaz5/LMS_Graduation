<x-layouts.app :pageTitle="__('Salary Transfers')">
    <style>
        .page-header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
        .page-header h2 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .page-header p { font-size: 13px; color: #64748b; }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; background: #4F46E5; color: white; border: none;
            border-radius: 10px; font-size: 13px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary:hover { background: #4338ca; }

        .stat-card {
            background: white; border-radius: 14px; padding: 20px;
            border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            margin-bottom: 24px; max-width: 280px;
        }
        .stat-label { font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; color: #059669; }

        .grid { display: grid; grid-template-columns: 1fr 380px; gap: 20px; }

        .table-card { background: white; border-radius: 14px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; }
        .table-header { padding: 20px; border-bottom: 1px solid #f1f5f9; }
        .table-title { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #0f172a; }
        .table-meta { font-size: 12px; color: #94a3b8; margin-top: 2px; }

        .filter-bar { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .filter-select { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 13px; font-family: 'DM Sans', sans-serif; color: #374151; background: #fafafa; outline: none; transition: border 0.2s; min-width: 160px; }
        .filter-select:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .btn-filter { padding: 8px 18px; background: #4F46E5; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s; }
        .btn-filter:hover { background: #4338ca; }

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

        .form-card { background: white; border-radius: 14px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 24px; }
        .form-title { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 16px; }
        .form-group { margin-bottom: 14px; }
        .form-label { font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px; display: block; }
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 9px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13.5px; font-family: 'DM Sans', sans-serif; color: #374151;
            background: #fafafa; outline: none; transition: border 0.2s; box-sizing: border-box;
        }
        .form-textarea { resize: vertical; min-height: 70px; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .btn-submit { width: 100%; padding: 10px; background: #4F46E5; color: white; border: none; border-radius: 10px; font-size: 13.5px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s; margin-top: 4px; }
        .btn-submit:hover { background: #4338ca; }

        .empty-state { text-align: center; padding: 56px 20px; }
        .empty-icon { width: 56px; height: 56px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }

        .pagination-row { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b; }

        @media (max-width: 900px) { .grid { grid-template-columns: 1fr; } }

        .wallet-tabs { display: flex; gap: 4px; margin-bottom: 24px; background: white; border-radius: 12px; padding: 6px; border: 1px solid #f1f5f9; width: fit-content; }
        .wallet-tab { padding: 10px 20px; border-radius: 8px; font-size: 13.5px; font-weight: 600; font-family: 'DM Sans', sans-serif; color: #64748b; text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .wallet-tab:hover { background: #f8fafc; color: #0f172a; }
        .wallet-tab.active { background: #4F46E5; color: white; }
        .wallet-tab svg { width: 16px; height: 16px; }
    </style>

    <div class="page-header">
        <div>
            <h2>{{ __("Salary Transfers") }}</h2>
            <p>{{ __("Record and track salary transfers to teachers") }}</p>
        </div>
    </div>

    <div class="wallet-tabs">
        <a href="{{ route('admin.wallet.index') }}" class="wallet-tab">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            {{ __("Payments") }}
        </a>
        <a href="{{ route('admin.wallet.tuition-fees') }}" class="wallet-tab">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.121 4.884 7.5 4 5 4v13c2.5 0 5.121.884 7 2.253M12 6.253C13.879 4.884 16.5 4 19 4v13c-2.5 0-5.121.884-7 2.253"/></svg>
            {{ __("Tuition Fees") }}
        </a>
        <a href="{{ route('admin.wallet.salaries') }}" class="wallet-tab active">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            {{ __("Salary Transfers") }}
        </a>
    </div>

    <div class="stat-card">
        <div class="stat-label">{{ __("Total Paid") }}</div>
        <div class="stat-value">{{ strtoupper($currency) }} {{ number_format((float) $totalPaid, 2) }}</div>
    </div>

    <div class="grid">
        {{-- Transfers list --}}
        <div class="table-card">
            <form method="GET" action="{{ route('admin.wallet.salaries') }}">
                <div class="filter-bar">
                    <div class="filter-group">
                        <label class="filter-label">{{ __("Teacher") }}</label>
                        <select name="teacher_id" class="filter-select" onchange="this.form.submit()">
                            <option value="">{{ __("All Teachers") }}</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected(request('teacher_id') == $teacher->id)>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">{{ __("Status") }}</label>
                        <select name="status" class="filter-select" onchange="this.form.submit()">
                            <option value="">{{ __("All") }}</option>
                            <option value="paid" @selected(request('status') === 'paid')>{{ __("Paid") }}</option>
                            <option value="pending" @selected(request('status') === 'pending')>{{ __("Pending") }}</option>
                            <option value="failed" @selected(request('status') === 'failed')>{{ __("Failed") }}</option>
                        </select>
                    </div>
                </div>
            </form>

            @if($transfers->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div style="font-family: 'Playfair Display', serif; font-size: 16px; color: #94a3b8; margin-bottom: 6px;">{{ __("No Transfers Found") }}</div>
                    <div style="font-size: 13px; color: #cbd5e1;">{{ __("No salary transfers have been recorded yet.") }}</div>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __("Teacher") }}</th>
                                <th>{{ __("Amount") }}</th>
                                <th>{{ __("Date") }}</th>
                                <th>{{ __("Description") }}</th>
                                <th>{{ __("Status") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transfers as $transfer)
                                <tr>
                                    <td style="font-weight: 600; color: #0f172a;">{{ $transfer->teacher?->name ?? '—' }}</td>
                                    <td style="font-weight: 600;">{{ strtoupper($transfer->currency) }} {{ number_format((float) $transfer->amount, 2) }}</td>
                                    <td>{{ $transfer->transfer_date?->format('M j, Y') }}</td>
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

        {{-- Create form --}}
        <div class="form-card">
            <div class="form-title">{{ __("Record Salary Transfer") }}</div>
            <form method="POST" action="{{ route('admin.wallet.salaries.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __("Teacher") }}</label>
                    <select name="teacher_user_id" class="form-select" required>
                        <option value="">{{ __("Select...") }}</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __("Amount") }}</label>
                    <input type="number" name="amount" class="form-input" step="0.01" min="0.01" placeholder="2500.00" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __("Currency") }}</label>
                    <input type="text" name="currency" class="form-input" value="{{ strtoupper(config('services.stripe.currency', 'usd')) }}" maxlength="3" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __("Transfer Date") }}</label>
                    <input type="date" name="transfer_date" class="form-input" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __("Description (optional)") }}</label>
                    <textarea name="description" class="form-textarea" placeholder="{{ __('Monthly salary, bonus, etc.') }}"></textarea>
                </div>
                <button type="submit" class="btn-submit">{{ __("Record Transfer") }}</button>
            </form>
        </div>
    </div>
</x-layouts.app>
