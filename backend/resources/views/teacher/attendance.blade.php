<x-layouts.app pageTitle="Take Attendance">
    <style>
        .page-header { margin-bottom: 20px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; }
        .page-desc { font-size: 13px; color: #64748b; margin-top: 2px; }

        .filter-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.7px; }
        .filter-select, .filter-input {
            padding: 9px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13.5px;
            font-family: 'DM Sans', sans-serif;
            color: #374151;
            background: #fafafa;
            outline: none;
            transition: border 0.2s;
            min-width: 180px;
        }
        .filter-select:focus, .filter-input:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }

        .table-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-title { font-size: 14px; font-weight: 700; color: #0f172a; }
        .table-meta { font-size: 12.5px; color: #94a3b8; }

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
            border-bottom: 1px solid #f8fafc;
            font-size: 13.5px;
            color: #374151;
        }
        td:first-child { padding-inline-start: 20px; }
        tbody tr:hover { background: #fafbff; }
        tbody tr:last-child td { border-bottom: none; }

        .student-cell { display: flex; align-items: center; gap: 10px; }
        .student-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4F46E5, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 12px; font-weight: 700; flex-shrink: 0;
        }
        .student-name { font-weight: 600; color: #0f172a; font-size: 13.5px; }

        .radio-group { display: flex; gap: 8px; flex-wrap: wrap; }
        .radio-label {
            display: flex; align-items: center; gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            font-size: 12px; font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .radio-label input[type="radio"] { display: none; }
        .radio-label.present { border-color: #10b981; color: #065f46; }
        .radio-label.present:has(input:checked) { background: #d1fae5; border-color: #10b981; }
        .radio-label.absent  { border-color: #ef4444; color: #991b1b; }
        .radio-label.absent:has(input:checked) { background: #fee2e2; border-color: #ef4444; }
        .radio-label.late    { border-color: #f59e0b; color: #78350f; }
        .radio-label.late:has(input:checked) { background: #fef3c7; border-color: #f59e0b; }
        .radio-label.excused { border-color: #6366f1; color: #3730a3; }
        .radio-label.excused:has(input:checked) { background: #eef2ff; border-color: #6366f1; }

        .submit-bar {
            padding: 16px 20px;
            border-top: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }
        .btn-submit {
            padding: 10px 28px;
            background: #4F46E5;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(79,70,229,0.3);
        }
        .btn-submit:hover { background: #4338ca; transform: translateY(-1px); }

        .empty-state {
            text-align: center; padding: 56px 20px;
        }
        .empty-icon {
            width: 56px; height: 56px;
            background: #f1f5f9; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .empty-title { font-weight: 700; color: #0f172a; font-size: 15px; margin-bottom: 4px; }
        .empty-desc { font-size: 13px; color: #94a3b8; }

        .bulk-select-bar {
            display: flex; gap: 8px; align-items: center;
        }
        .btn-quick {
            padding: 5px 12px;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            font-size: 12px; font-weight: 600;
            cursor: pointer;
            background: white;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.15s;
        }
        .btn-quick:hover { border-color: #4F46E5; color: #4F46E5; }
    </style>

    <div class="page-header">
        <div class="page-title">Take Attendance</div>
        <div class="page-desc">Select a classroom and date to record student attendance</div>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 12px 16px; border-radius: 10px; font-size: 13.5px; font-weight: 500; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('teacher.attendance') }}">
        <div class="filter-card">
            <div class="filter-group">
                <label class="filter-label">Classroom</label>
                <select name="classroom_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">— Select Classroom —</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" @selected($selectedClassroomId == $classroom->id)>
                            {{ $classroom->name }} ({{ $classroom->grade->name ?? '—' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Date</label>
                <input type="date" name="date" class="filter-input" value="{{ $selectedDate }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>

    @if($selectedClassroomId && $students->isEmpty())
        <div class="table-card">
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                </div>
                <div class="empty-title">No Students Found</div>
                <div class="empty-desc">This classroom has no enrolled students.</div>
            </div>
        </div>
    @elseif($students->isNotEmpty())
        <form method="POST" action="{{ route('teacher.attendance.store') }}">
            @csrf
            <input type="hidden" name="classroom_id" value="{{ $selectedClassroomId }}">
            <input type="hidden" name="date" value="{{ $selectedDate }}">

            @error('statuses')
                <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 12px;">{{ $message }}</div>
            @enderror

            <div class="table-card">
                <div class="table-header">
                    <div>
                        <div class="table-title">
                            {{ $classrooms->firstWhere('id', $selectedClassroomId)?->name ?? 'Classroom' }}
                            — {{ \Carbon\Carbon::parse($selectedDate)->format('D, M j, Y') }}
                        </div>
                        <div class="table-meta">{{ $students->count() }} students</div>
                    </div>
                    <div class="bulk-select-bar">
                        <span style="font-size: 12px; color: #94a3b8; margin-right: 4px;">Mark all:</span>
                        <button type="button" class="btn-quick" onclick="markAll('present')">Present</button>
                        <button type="button" class="btn-quick" onclick="markAll('absent')">Absent</button>
                        <button type="button" class="btn-quick" onclick="markAll('late')">Late</button>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $i => $profile)
                                @php $existing = $existingAttendance->get($profile->user_id); @endphp
                                <tr>
                                    <td style="color: #94a3b8; font-size: 12.5px;">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar">{{ strtoupper(substr($profile->student->name ?? '?', 0, 2)) }}</div>
                                            <div class="student-name">{{ $profile->student->name ?? '—' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="radio-group" data-student="{{ $profile->user_id }}">
                                            @foreach(['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'excused' => 'Excused'] as $val => $label)
                                                <label class="radio-label {{ $val }}">
                                                    <input type="radio"
                                                           name="statuses[{{ $profile->user_id }}]"
                                                           value="{{ $val }}"
                                                           @checked(($existing?->status ?? 'present') === $val)>
                                                    {{ $label }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="submit-bar">
                    <div style="font-size: 13px; color: #64748b;">
                        @if($existingAttendance->isNotEmpty())
                            <span style="color: #10b981; font-weight: 600;">✓ Attendance already recorded</span> — submitting will update existing records.
                        @else
                            Recording attendance for <strong>{{ $students->count() }}</strong> students.
                        @endif
                    </div>
                    <button type="submit" class="btn-submit">Save Attendance</button>
                </div>
            </div>
        </form>
    @else
        <div class="table-card">
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div class="empty-title">Select a Classroom</div>
                <div class="empty-desc">Choose a classroom and date above to start recording attendance.</div>
            </div>
        </div>
    @endif

    <script>
    function markAll(status) {
        document.querySelectorAll('.radio-group').forEach(group => {
            const radio = group.querySelector(`input[value="${status}"]`);
            if (radio) radio.checked = true;
        });
    }
    </script>
</x-layouts.app>
