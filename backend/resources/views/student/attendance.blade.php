<x-layouts.app pageTitle="My Attendance">
    <style>
        .page-header { margin-bottom: 20px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; }
        .page-desc { font-size: 13px; color: #64748b; margin-top: 2px; }

        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
        @media(max-width: 768px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }

        .stat-card {
            background: white; border-radius: 12px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 16px 18px;
            display: flex; align-items: center; gap: 12px;
        }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-value { font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1; }
        .stat-label { font-size: 11.5px; color: #94a3b8; font-weight: 600; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; }

        .filter-card {
            background: white; border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.7px; }
        .filter-input {
            padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13px; font-family: 'DM Sans', sans-serif; color: #374151;
            background: #fafafa; outline: none; transition: border 0.2s;
        }
        .filter-input:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .btn-filter {
            padding: 8px 18px; background: #4F46E5; color: white; border: none;
            border-radius: 8px; font-size: 13px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
        }
        .btn-filter:hover { background: #4338ca; }

        .table-card {
            background: white; border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .table-header {
            padding: 18px 20px; border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }
        .table-title { font-size: 14px; font-weight: 700; color: #0f172a; }
        .table-meta { font-size: 12.5px; color: #94a3b8; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th {
            padding: 12px 16px; text-align: start;
            font-size: 11px; font-weight: 700; color: #94a3b8;
            letter-spacing: 0.8px; text-transform: uppercase;
        }
        th:first-child { padding-inline-start: 20px; }
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #f8fafc;
            font-size: 13.5px; color: #374151;
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
        .badge-present  { background: #d1fae5; color: #059669; }
        .badge-absent   { background: #fee2e2; color: #dc2626; }
        .badge-late     { background: #fef3c7; color: #d97706; }
        .badge-excused  { background: #eef2ff; color: #4F46E5; }
        .badge-pending  { background: #fef9c3; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #059669; }
        .badge-rejected { background: #fee2e2; color: #dc2626; }
        .badge-none     { background: #f1f5f9; color: #94a3b8; }

        .pagination-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 20px; border-top: 1px solid #f1f5f9;
            font-size: 13px; color: #64748b;
        }

        .empty-state { text-align: center; padding: 56px 20px; }
        .empty-icon {
            width: 56px; height: 56px; background: #f1f5f9; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
        }
        .empty-title { font-weight: 700; color: #0f172a; font-size: 15px; margin-bottom: 4px; }
        .empty-desc { font-size: 13px; color: #94a3b8; }
    </style>

    <div class="page-header">
        <div class="page-title">My Attendance</div>
        <div class="page-desc">View your attendance history and justification statuses</div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        @php
            $total   = $summary->sum();
            $present = $summary->get('present', 0);
            $absent  = $summary->get('absent', 0) + $summary->get('excused', 0);
            $late    = $summary->get('late', 0);
            $rate    = $total > 0 ? round(($present / $total) * 100) : 0;
        @endphp
        <div class="stat-card">
            <div class="stat-icon" style="background: #eef2ff;">
                <svg width="20" height="20" fill="none" stroke="#4F46E5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div><div class="stat-value">{{ $total }}</div><div class="stat-label">Total Days</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #d1fae5;">
                <svg width="20" height="20" fill="none" stroke="#059669" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div><div class="stat-value" style="color: #059669;">{{ $present }}</div><div class="stat-label">Present</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fee2e2;">
                <svg width="20" height="20" fill="none" stroke="#dc2626" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div><div class="stat-value" style="color: #dc2626;">{{ $absent }}</div><div class="stat-label">Absent</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7;">
                <svg width="20" height="20" fill="none" stroke="#d97706" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div><div class="stat-value" style="color: #d97706;">{{ $rate }}%</div><div class="stat-label">Rate</div></div>
        </div>
    </div>

    {{-- Date filter --}}
    <form method="GET" action="{{ route('student.attendance') }}">
        <div class="filter-card">
            <div class="filter-group">
                <label class="filter-label">From</label>
                <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">To</label>
                <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="btn-filter">Filter</button>
            @if(request('date_from') || request('date_to'))
                <a href="{{ route('student.attendance') }}" style="align-self: flex-end; padding: 8px 14px; font-size: 12.5px; color: #64748b; text-decoration: none;">Clear</a>
            @endif
        </div>
    </form>

    <div class="table-card">
        <div class="table-header">
            <div class="table-title">Attendance History</div>
            <div class="table-meta">{{ $records->total() }} records</div>
        </div>

        @if($records->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div class="empty-title">No Records Found</div>
                <div class="empty-desc">No attendance records match the selected filters.</div>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Justification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">{{ $record->date?->format('M j, Y') }}</div>
                                    <div style="font-size: 11.5px; color: #94a3b8;">{{ $record->date?->format('l') }}</div>
                                </td>
                                <td style="color: #475569;">{{ $record->scheduleSlot?->subject?->name ?? '—' }}</td>
                                <td><span class="badge badge-{{ $record->status }}">{{ ucfirst($record->status) }}</span></td>
                                <td>
                                    @if($record->justification)
                                        <span class="badge badge-{{ $record->justification->status }}">{{ ucfirst($record->justification->status) }}</span>
                                    @elseif($record->status === 'absent')
                                        <span class="badge badge-none" style="font-size: 11px;">No justification</span>
                                    @else
                                        <span style="color: #cbd5e1; font-size: 12px;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($records->hasPages())
                <div class="pagination-row">
                    <div>Page {{ $records->currentPage() }} of {{ $records->lastPage() }}</div>
                    <div style="display: flex; gap: 6px;">
                        @if($records->onFirstPage())
                            <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">← Prev</span>
                        @else
                            <a href="{{ $records->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; border: 1px solid #e2e8f0; color: #374151; text-decoration: none; font-size: 12px; font-weight: 600;">← Prev</a>
                        @endif
                        @if($records->hasMorePages())
                            <a href="{{ $records->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #4F46E5; color: white; text-decoration: none; font-size: 12px; font-weight: 600;">Next →</a>
                        @else
                            <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">Next →</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
