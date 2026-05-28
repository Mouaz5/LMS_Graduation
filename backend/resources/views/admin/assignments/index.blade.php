<x-layouts.app pageTitle="Teacher Assignments">
    <style>
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
        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            gap: 12px;
            flex-wrap: wrap;
        }
        .search-input {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            color: #374151;
            outline: none;
            background: #fafafa;
            width: 220px;
            transition: all 0.2s;
        }
        .search-input:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
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
            padding: 12px 16px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid #f8fafc;
        }
        td:first-child { padding-inline-start: 20px; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #cbd5e1;
        }
    </style>

    <div class="page-actions">
        <div>
            <div style="font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a;">Teacher Assignments</div>
            <div class="page-desc">Manage teacher-subject-classroom assignments</div>
        </div>
        <a href="{{ route('admin.assignments.create') }}" class="btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Assignment
        </a>
    </div>

    <div class="table-card">
        <div class="table-toolbar">
            <input type="text" class="search-input" placeholder="Search assignments..." oninput="filterTable(this.value)">
            <div class="table-meta">{{ $assignments->total() }} assignments total</div>
        </div>

        <div style="overflow-x: auto;">
            <table id="assignmentsTable">
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Subject</th>
                        <th>Classroom</th>
                        <th>Academic Year</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $a)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #eef2ff; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #4338ca;">
                                        {{ collect(explode(' ', $a->teacher->name))->map(fn($w) => strtoupper($w[0]))->take(2)->join('') }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600;">{{ $a->teacher->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $a->subject->name }}</div>
                                <div style="font-size: 12px; color: #94a3b8;">{{ $a->subject->code }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $a->classroom->name }}</div>
                                <div style="font-size: 12px; color: #94a3b8;">{{ $a->classroom->grade->name }}</div>
                            </td>
                            <td>
                                <span class="badge" style="background: #f0fdf4; color: #166534;">{{ $a->academicYear->name }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">
                                <svg width="40" height="40" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                No assignments found. Create one to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
        <div class="pagination-row">
            <div>Page {{ $assignments->currentPage() }} of {{ $assignments->lastPage() }}</div>
            <div style="display: flex; gap: 6px; align-items: center;">
                @if($assignments->onFirstPage())
                    <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">&larr; Prev</span>
                @else
                    <a href="{{ $assignments->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; border: 1px solid #e2e8f0; color: #374151; text-decoration: none; font-size: 12px; font-weight: 600; transition: all 0.2s;">&larr; Prev</a>
                @endif

                @if($assignments->hasMorePages())
                    <a href="{{ $assignments->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #4F46E5; color: white; text-decoration: none; font-size: 12px; font-weight: 600; box-shadow: 0 2px 6px rgba(79,70,229,0.3);">Next &rarr;</a>
                @else
                    <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">Next &rarr;</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    <script>
    function filterTable(query) {
        const rows = document.querySelectorAll('#assignmentsTable tbody tr');
        const q = query.toLowerCase();
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
    </script>
</x-layouts.app>
