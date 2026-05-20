<x-layouts.app pageTitle="Schedule Builder">
    <style>
        .controls-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .controls-bar select {
            padding: 9px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            font-family: 'DM Sans', sans-serif;
            background: white;
            color: var(--text-primary);
            cursor: pointer;
            min-width: 200px;
        }
        .controls-bar select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }

        .schedule-grid { overflow-x: auto; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        table.grid-table { width: 100%; border-collapse: collapse; background: white; }
        .grid-table th, .grid-table td { border: 1px solid var(--border); }
        .grid-table thead th {
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            padding: 13px 18px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            text-align: center;
        }
        .grid-table tbody th {
            background: #f8fafc;
            color: var(--text-secondary);
            padding: 10px 16px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            min-width: 90px;
            text-align: center;
        }
        .grid-cell {
            min-width: 170px;
            height: 80px;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }
        .grid-cell.empty {
            cursor: pointer;
            transition: background 0.15s;
        }
        .grid-cell.empty:hover { background: #eef2ff; }
        .grid-cell.filled { background: #f0fdf4; }
        .cell-add { font-size: 24px; color: #c7d2fe; line-height: 1; }
        .cell-content { display: flex; flex-direction: column; gap: 2px; align-items: center; }
        .cell-subject { color: #4338ca; font-size: 12.5px; font-weight: 700; }
        .cell-teacher { color: var(--text-secondary); font-size: 11px; }
        .cell-time { color: var(--text-muted); font-size: 10.5px; }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.5);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: white;
            border-radius: 18px;
            padding: 32px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.18);
        }
        .modal-header { margin-bottom: 24px; }
        .modal-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 19px;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        .modal-header p { font-size: 13px; color: var(--text-muted); }
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-group select,
        .form-group input[type="time"] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-primary);
            background: white;
        }
        .form-group select:focus,
        .form-group input[type="time"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; }
        .btn-cancel {
            padding: 10px 20px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            font-family: 'DM Sans', sans-serif;
            background: white;
            color: var(--text-secondary);
            cursor: pointer;
        }
        .btn-cancel:hover { border-color: #94a3b8; }
        .btn-save {
            padding: 10px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(79,70,229,0.3);
        }
        .btn-save:hover { background: var(--primary-dark); }
        .error-banner {
            margin-bottom: 16px;
            padding: 12px 16px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            color: #dc2626;
            font-size: 13px;
        }
        .hint-box {
            padding: 48px 0;
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
            background: white;
            border-radius: 14px;
            border: 1px solid var(--border);
        }
    </style>

    {{-- Page header --}}
    <div style="margin-bottom: 24px;">
        <div style="font-family:'Playfair Display',serif; font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">
            Schedule Builder
        </div>
        <div style="font-size:13px; color:var(--text-secondary);">
            Build and manage weekly class timetables. Select a classroom and semester to view or edit.
        </div>
    </div>

    {{-- Validation error --}}
    @if($errors->any())
        <div class="error-banner">{{ $errors->first() }}</div>
    @endif

    {{-- Classroom + Semester selectors — submits as GET to reload page with data --}}
    <form method="GET" action="{{ route('admin.schedule.index') }}" class="controls-bar">
        <select name="classroom_id" onchange="this.form.submit()">
            <option value="">— Select Classroom —</option>
            @foreach($classrooms as $cr)
                <option value="{{ $cr->id }}" @selected($classroomId == $cr->id)>
                    {{ $cr->name }} ({{ $cr->grade->name }})
                </option>
            @endforeach
        </select>
        <select name="semester_id" onchange="this.form.submit()">
            <option value="">— Select Semester —</option>
            @foreach($semesters as $sem)
                <option value="{{ $sem->id }}" @selected($semesterId == $sem->id)>
                    {{ $sem->name }} — {{ $sem->academicYear->name }}
                </option>
            @endforeach
        </select>
    </form>

    @if(!$classroomId || !$semesterId)
        <div class="hint-box">Select a classroom and semester above to view or build the schedule.</div>
    @else
        {{-- Schedule grid --}}
        <div class="schedule-grid">
            <table class="grid-table">
                <thead>
                    <tr>
                        <th style="min-width:90px;">Period</th>
                        <th>Sunday</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(range(1, 8) as $period)
                        <tr>
                            <th>P{{ $period }}</th>
                            @foreach(['sunday','monday','tuesday','wednesday','thursday'] as $day)
                                @php $slot = $slots[$day . '_' . $period] ?? null; @endphp
                                <td class="grid-cell {{ $slot ? 'filled' : 'empty' }}"
                                    @if(!$slot) onclick="openModal('{{ $day }}', {{ $period }})" @endif>
                                    @if($slot)
                                        <div class="cell-content">
                                            <span class="cell-subject">{{ $slot->subject->name }}</span>
                                            <span class="cell-teacher">{{ $slot->teacher->name }}</span>
                                            <span class="cell-time">{{ substr($slot->start_time, 0, 5) }} – {{ substr($slot->end_time, 0, 5) }}</span>
                                        </div>
                                    @else
                                        <div class="cell-content">
                                            <span class="cell-add">+</span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Assignment modal --}}
    <div class="modal-overlay" id="slotModal">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modalTitle">Assign Slot</h3>
                <p id="modalSubtitle">Choose a teacher and subject for this period.</p>
            </div>

            <form method="POST" action="{{ route('admin.schedule.store') }}">
                @csrf
                <input type="hidden" name="classroom_id" value="{{ $classroomId }}">
                <input type="hidden" name="semester_id"  value="{{ $semesterId }}">
                <input type="hidden" name="day_of_week"  id="modalDay">
                <input type="hidden" name="period_number" id="modalPeriod">

                <div class="form-group">
                    <label>Teacher</label>
                    <select name="teacher_user_id" required>
                        <option value="">— Select Teacher —</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <select name="subject_id" required>
                        <option value="">— Select Subject —</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" name="start_time" step="300" required>
                    </div>
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="time" name="end_time" step="300" required>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save Slot</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(day, period) {
            const label = day.charAt(0).toUpperCase() + day.slice(1);
            document.getElementById('modalTitle').textContent   = `Assign — ${label}, Period ${period}`;
            document.getElementById('modalSubtitle').textContent = 'Choose a teacher and subject for this period.';
            document.getElementById('modalDay').value    = day;
            document.getElementById('modalPeriod').value = period;
            document.getElementById('slotModal').classList.add('open');
        }

        function closeModal() {
            document.getElementById('slotModal').classList.remove('open');
        }

        document.getElementById('slotModal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        // Re-open modal if there were validation errors (form was rejected)
        @if($errors->any() && old('day_of_week'))
            openModal('{{ old('day_of_week') }}', {{ old('period_number') }});
        @endif
    </script>
</x-layouts.app>
