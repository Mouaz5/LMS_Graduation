<x-layouts.app :pageTitle="__('Children\'s Attendance')">
    <style>
        .page-header { margin-bottom: 20px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; }

        .child-selector {
            background: white; border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;
        }
        .filter-select, .filter-input {
            padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13px; font-family: 'DM Sans', sans-serif; color: #374151;
            background: #fafafa; outline: none; transition: border 0.2s; min-width: 180px;
        }
        .filter-select:focus, .filter-input:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .btn-filter {
            padding: 8px 18px; background: #4F46E5; color: white; border: none;
            border-radius: 8px; font-size: 13px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
        }
        .btn-filter:hover { background: #4338ca; }

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

        .btn-justify {
            padding: 5px 12px;
            background: #4F46E5; color: white; border: none; border-radius: 8px;
            font-size: 11.5px; font-weight: 600; font-family: 'DM Sans', sans-serif;
            cursor: pointer; transition: all 0.15s; white-space: nowrap;
        }
        .btn-justify:hover { background: #4338ca; }

        /* Modal overlay */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.4); z-index: 100;
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: white; border-radius: 16px;
            width: 100%; max-width: 460px;
            padding: 28px; margin: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .modal-title { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
        .modal-subtitle { font-size: 13px; color: #64748b; margin-bottom: 20px; }
        .modal-label { font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px; display: block; }
        .modal-input, .modal-textarea {
            width: 100%; padding: 9px 14px;
            border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13.5px; font-family: 'DM Sans', sans-serif; color: #374151;
            background: #fafafa; outline: none; transition: border 0.2s;
            box-sizing: border-box; margin-bottom: 14px;
        }
        .modal-textarea { resize: vertical; min-height: 80px; }
        .modal-input:focus, .modal-textarea:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .modal-actions { display: flex; gap: 10px; margin-top: 4px; }
        .btn-submit {
            flex: 1; padding: 10px;
            background: #4F46E5; color: white; border: none; border-radius: 10px;
            font-size: 13.5px; font-weight: 600; font-family: 'DM Sans', sans-serif;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-submit:hover { background: #4338ca; }
        .btn-cancel {
            padding: 10px 18px;
            background: #f8fafc; color: #64748b; border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 13.5px; font-weight: 600; font-family: 'DM Sans', sans-serif;
            cursor: pointer; transition: all 0.15s;
        }
        .btn-cancel:hover { background: #f1f5f9; }

        .empty-state { text-align: center; padding: 56px 20px; }
        .empty-icon {
            width: 56px; height: 56px; background: #f1f5f9; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
        }
    </style>

    <div class="page-header">
        <div class="page-title">{{ __("Children's Attendance") }}</div>
        <div class="page-desc">{{ __("View your child's absence history and submit justifications") }}</div>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 12px 16px; border-radius: 10px; font-size: 13.5px; font-weight: 500; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Child + date selector --}}
    <form method="GET" action="{{ route('parent.attendance') }}">
        <div class="child-selector">
            @if($children->count() > 1)
                <div class="filter-group">
                    <label class="filter-label">{{ __("Child") }}</label>
                    <select name="child_id" class="filter-select" onchange="this.form.submit()">
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" @selected($selectedChild?->id == $child->id)>
                                {{ $child->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="filter-group">
                <label class="filter-label">{{ __("From") }}</label>
                <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">{{ __("To") }}</label>
                <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
            </div>
            @if($selectedChild)
                <input type="hidden" name="child_id" value="{{ $selectedChild->id }}">
            @endif
            <button type="submit" class="btn-filter">{{ __("Filter") }}</button>
            @if(request('date_from') || request('date_to'))
                <a href="{{ route('parent.attendance', ['child_id' => $selectedChild?->id]) }}" style="align-self: flex-end; padding: 8px 14px; font-size: 12.5px; color: #64748b; text-decoration: none;">{{ __("Clear") }}</a>
            @endif
        </div>
    </form>

    @if(!$selectedChild)
        <div class="table-card">
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                </div>
                <div class="empty-title">{{ __("No Children Linked") }}</div>
                <div class="empty-desc">{{ __("No children are linked to your account yet.") }}</div>
            </div>
        </div>
    @else
        <div class="table-card">
            <div class="table-header">
                <div>
                    <div class="table-title">{{ __(":name's Attendance", ['name' => $selectedChild->name]) }}</div>
                    <div class="table-meta">{{ __(":count records", ['count' => $records->total()]) }}</div>
                </div>
            </div>

            @if($records->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div class="empty-title">{{ __("No Records Found") }}</div>
                    <div class="empty-desc">{{ __("No attendance has been recorded yet for this period.") }}</div>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __("Date") }}</th>
                                <th>{{ __("Subject") }}</th>
                                <th>{{ __("Status") }}</th>
                                <th>{{ __("Justification") }}</th>
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
                                        @elseif($record->isAbsent())
                                            <button type="button" class="btn-justify"
                                                    onclick="openJustifyModal({{ $record->id }}, '{{ $record->date?->format('M j, Y') }}')">
                                                {{ __("Submit Justification") }}
                                            </button>
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
                        <div>{{ __("Page :current of :last", ['current' => $records->currentPage(), 'last' => $records->lastPage()]) }}</div>
                        <div style="display: flex; gap: 6px;">
                            @if($records->onFirstPage())
                                <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">← {{ __("Prev") }}</span>
                            @else
                                <a href="{{ $records->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; border: 1px solid #e2e8f0; color: #374151; text-decoration: none; font-size: 12px; font-weight: 600;">← {{ __("Prev") }}</a>
                            @endif
                            @if($records->hasMorePages())
                                <a href="{{ $records->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #4F46E5; color: white; text-decoration: none; font-size: 12px; font-weight: 600;">{{ __("Next") }} →</a>
                            @else
                                <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">{{ __("Next") }} →</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif

    {{-- Justification Modal --}}
    <div class="modal-overlay" id="justifyModal">
        <div class="modal-box">
            <div class="modal-title">{{ __("Submit Justification") }}</div>
            <div class="modal-subtitle" id="modalSubtitle">{{ __("Absence on") }} —</div>

            <form id="justifyForm" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="modal-label">{{ __("Reason") }}</label>
                <textarea name="reason" class="modal-textarea" placeholder="{{ __('Explain the reason for the absence...') }}" required></textarea>

                <label class="modal-label">{{ __("Supporting Document (optional)") }}</label>
                <input type="file" name="document" class="modal-input" accept=".pdf,.jpg,.jpeg,.png">

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeJustifyModal()">{{ __("Cancel") }}</button>
                    <button type="submit" class="btn-submit">{{ __("Submit") }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openJustifyModal(attendanceId, dateStr) {
        document.getElementById('justifyModal').classList.add('open');
        document.getElementById('modalSubtitle').textContent = 'Absence on ' + dateStr;
        document.getElementById('justifyForm').action = '/parent/attendance/' + attendanceId + '/justify';
    }
    function closeJustifyModal() {
        document.getElementById('justifyModal').classList.remove('open');
    }
    document.getElementById('justifyModal').addEventListener('click', function(e) {
        if (e.target === this) closeJustifyModal();
    });
    </script>
</x-layouts.app>
