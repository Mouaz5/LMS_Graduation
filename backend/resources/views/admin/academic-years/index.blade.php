<x-layouts.app pageTitle="Academic Years">
    <style>
        .page-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-desc { font-size: 13px; color: #64748b; }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 20px;
            background: #4F46E5;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(79,70,229,0.3);
        }
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(79,70,229,0.4);
        }
        .btn-primary svg { width: 15px; height: 15px; }
        .table-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th {
            padding: 12px 16px;
            text-align: start;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        th:first-child { padding-inline-start: 20px; }
        td {
            padding: 14px 16px;
            font-size: 13.5px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        td:first-child { padding-inline-start: 20px; font-weight: 600; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-active {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-inactive {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .empty-state {
            text-align: center;
            padding: 48px;
            color: #cbd5e1;
            font-size: 14px;
        }
    </style>

    <div class="page-actions">
        <div>
            <div style="font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a;">Academic Years</div>
            <div class="page-desc">Manage academic years and semesters</div>
        </div>
        <a href="{{ route('admin.academic-years.create') }}" class="btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Academic Year
        </a>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Semesters</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($years as $year)
                    <tr style="cursor: pointer; transition: background 0.15s;"
                        onclick="window.location='{{ route('admin.academic-years.show', $year) }}'"
                        onmouseover="this.style.background='#fafbff'"
                        onmouseout="this.style.background='transparent'">
                        <td>{{ $year->name }}</td>
                        <td>{{ $year->start_date->format('M d, Y') }}</td>
                        <td>{{ $year->end_date->format('M d, Y') }}</td>
                        <td>{{ $year->semesters_count }}</td>
                        <td>
                            <span class="badge {{ $year->is_active ? 'badge-active' : 'badge-inactive' }}">
                                {{ $year->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">No academic years found. Create one to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
