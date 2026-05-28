<x-layouts.app pageTitle="Absence Justifications">
    <style>
        .page-header { margin-bottom: 20px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; }

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
            vertical-align: middle;
        }
        td:first-child { padding-inline-start: 20px; }
        tbody tr:hover { background: #fafbff; }
        tbody tr:last-child td { border-bottom: none; }

        .student-cell { display: flex; align-items: center; gap: 10px; }
        .student-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #4F46E5, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 11px; font-weight: 700; flex-shrink: 0;
        }
        .student-name { font-weight: 600; color: #0f172a; font-size: 13px; }
        .student-class { font-size: 11.5px; color: #94a3b8; }

        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11.5px; font-weight: 600;
        }
        .badge-absent  { background: #fee2e2; color: #991b1b; }
        .badge-pending { background: #fef3c7; color: #78350f; }

        .reason-text {
            max-width: 240px;
            font-size: 12.5px;
            color: #475569;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .action-forms { display: flex; gap: 6px; align-items: center; }
        .btn-approve, .btn-reject {
            padding: 6px 14px;
            border-radius: 8px;
            border: none;
            font-size: 12px; font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn-approve { background: #d1fae5; color: #065f46; }
        .btn-approve:hover { background: #a7f3d0; }
        .btn-reject  { background: #fee2e2; color: #991b1b; }
        .btn-reject:hover  { background: #fecaca; }

        .doc-link {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 12px; color: #4F46E5; text-decoration: none; font-weight: 600;
        }
        .doc-link:hover { text-decoration: underline; }

        .empty-state { text-align: center; padding: 56px 20px; }
        .empty-icon {
            width: 56px; height: 56px; background: #f1f5f9; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
        }
    </style>

    <div class="page-header">
        <div class="page-title">Absence Justifications</div>
        <div class="page-desc">Review and approve or reject pending parent justifications</div>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 12px 16px; border-radius: 10px; font-size: 13.5px; font-weight: 500; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="table-card">
        <div class="table-header">
            <div class="table-title">Pending Justifications</div>
            <div class="table-meta">{{ $justifications->total() }} pending</div>
        </div>

        @if($justifications->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="empty-title">All Clear!</div>
                <div class="empty-desc">No pending justifications to review.</div>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Submitted By</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($justifications as $j)
                            <tr>
                                <td>
                                    <div class="student-cell">
                                        <div class="student-avatar">{{ strtoupper(substr($j->attendance->student->name ?? '?', 0, 2)) }}</div>
                                        <div>
                                            <div class="student-name">{{ $j->attendance->student->name ?? '—' }}</div>
                                            <div class="student-class">{{ $j->attendance->classroom->name ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">{{ $j->attendance->date?->format('M j, Y') }}</div>
                                    <span class="badge badge-absent">Absent</span>
                                </td>
                                <td>
                                    <div class="reason-text">{{ $j->reason }}</div>
                                </td>
                                <td style="font-size: 12.5px; color: #475569;">{{ $j->submittedBy->name ?? '—' }}</td>
                                <td>
                                    @if($j->document_url)
                                        <a href="{{ $j->document_url }}" target="_blank" class="doc-link">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            View
                                        </a>
                                    @else
                                        <span style="color: #cbd5e1; font-size: 12px;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-forms">
                                        <form method="POST" action="{{ route('teacher.justifications.approve', $j) }}">
                                            @csrf
                                            <button type="submit" class="btn-approve">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('teacher.justifications.reject', $j) }}">
                                            @csrf
                                            <button type="submit" class="btn-reject">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($justifications->hasPages())
                <div class="pagination-row">
                    <div>Page {{ $justifications->currentPage() }} of {{ $justifications->lastPage() }}</div>
                    <div style="display: flex; gap: 6px;">
                        @if($justifications->onFirstPage())
                            <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">← Prev</span>
                        @else
                            <a href="{{ $justifications->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; border: 1px solid #e2e8f0; color: #374151; text-decoration: none; font-size: 12px; font-weight: 600;">← Prev</a>
                        @endif
                        @if($justifications->hasMorePages())
                            <a href="{{ $justifications->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #4F46E5; color: white; text-decoration: none; font-size: 12px; font-weight: 600;">Next →</a>
                        @else
                            <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">Next →</span>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
