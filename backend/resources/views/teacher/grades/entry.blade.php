<x-layouts.app pageTitle="Grade Entry">
<style>
    .page-header { margin-bottom: 20px; }
    .page-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; }

    .filter-card {
        background: white; border-radius: 14px; border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 20px; margin-bottom: 20px;
        display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;
    }
    .filter-select {
        padding: 9px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13.5px; font-family: 'DM Sans', sans-serif; color: #374151;
        background: #fafafa; outline: none; transition: border 0.2s; min-width: 180px;
    }

    .btn-primary {
        padding: 9px 20px; background: #4F46E5; color: white; border: none; border-radius: 8px;
        font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background 0.2s;
    }
    .btn-primary:hover { background: #3730a3; }

    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #f8fafc; }
    th { padding: 12px 16px; text-align: start; font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: 0.8px; text-transform: uppercase; }
    td { padding: 10px 16px; border-bottom: 1px solid #f8fafc; font-size: 13.5px; color: #374151; }
    tbody tr:last-child td { border-bottom: none; }

    .score-input {
        width: 90px; padding: 7px 10px; border: 1.5px solid #e2e8f0; border-radius: 7px;
        font-size: 13px; font-family: 'DM Sans', sans-serif; text-align: center;
        outline: none; transition: border 0.2s;
    }
    .score-input:focus { border-color: #4F46E5; box-shadow: 0 0 0 2px rgba(79,70,229,0.1); }
    .score-input.invalid { border-color: #ef4444; }

    .max-score-wrap { display: flex; align-items: center; gap: 8px; padding: 18px 20px; border-top: 1px solid #f1f5f9; }
    .form-actions { padding: 16px 20px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; }

    .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 500; }
    .alert-success { background: #dcfce7; color: #166534; }
    .alert-error { background: #fee2e2; color: #991b1b; }

    .empty-state { padding: 48px 20px; text-align: center; color: #94a3b8; }
    .empty-icon { font-size: 40px; margin-bottom: 10px; }
</style>

<div class="page-header">
    <div class="page-title">Grade Entry</div>
    <div class="page-desc">Enter student scores per subject and exam type</div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

{{-- Filters --}}
<form method="GET" action="{{ route('teacher.grades.entry') }}" id="filterForm">
    <div class="filter-card">
        <div class="filter-group">
            <label class="filter-label">Semester</label>
            <select class="filter-select" name="semester_id" onchange="this.form.submit()">
                <option value="">Select semester…</option>
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" {{ $selectedSemesterId == $sem->id ? 'selected' : '' }}>
                        {{ $sem->academicYear->name ?? '' }} — {{ $sem->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Subject</label>
            <select class="filter-select" name="subject_id" onchange="this.form.submit()">
                <option value="">Select subject…</option>
                @foreach($assignments->unique('subject_id') as $a)
                    <option value="{{ $a->subject_id }}" {{ $selectedSubjectId == $a->subject_id ? 'selected' : '' }}>
                        {{ $a->subject->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Classroom</label>
            <select class="filter-select" name="classroom_id" onchange="this.form.submit()">
                <option value="">Select classroom…</option>
                @foreach($assignments as $a)
                    <option value="{{ $a->classroom_id }}" {{ $selectedClassroomId == $a->classroom_id ? 'selected' : '' }}>
                        {{ $a->classroom->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Exam Type</label>
            <select class="filter-select" name="exam_type_id" onchange="this.form.submit()">
                <option value="">Select exam type…</option>
                @foreach($examTypes as $et)
                    <option value="{{ $et->id }}" {{ $selectedExamTypeId == $et->id ? 'selected' : '' }}>
                        {{ $et->name }} ({{ $et->weight_percent }}%)
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</form>

{{-- Grade Table --}}
@if($selectedSubjectId && $selectedClassroomId && $selectedExamTypeId)
    <form method="POST" action="{{ route('teacher.grades.store') }}" id="gradeForm">
        @csrf
        <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
        <input type="hidden" name="exam_type_id" value="{{ $selectedExamTypeId }}">

        <div class="table-card">
            <div class="table-header">
                <span class="table-title">Student Scores</span>
                <span class="table-meta">{{ $students->count() }} students</span>
            </div>

            <div class="max-score-wrap">
                <label style="font-size:13px; font-weight:600; color:#374151;">Max Score:</label>
                <input type="number" name="max_score" id="maxScore" value="{{ $existingGrades->first()?->max_score ?? 100 }}"
                    step="0.01" min="0.01" style="width:90px; padding:7px 10px; border:1.5px solid #e2e8f0; border-radius:7px; font-size:13px; font-family:'DM Sans',sans-serif; outline:none;">
            </div>

            @if($students->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Score</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $student)
                            @php $existing = $existingGrades->get($student->id); @endphp
                            <tr>
                                <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                                <td style="font-weight:600;">{{ $student->name }}</td>
                                <td>
                                    <input type="number" class="score-input" step="0.01" min="0"
                                        name="scores[{{ $student->id }}]"
                                        value="{{ $existing?->score }}"
                                        placeholder="—"
                                        data-student="{{ $student->id }}">
                                </td>
                                <td>
                                    @if($existing)
                                        <span style="font-size:11px; background:#dcfce7; color:#166534; padding:2px 8px; border-radius:99px; font-weight:700;">
                                            Saved ({{ $existing->score }}/{{ $existing->max_score }})
                                        </span>
                                    @else
                                        <span style="font-size:11px; background:#f1f5f9; color:#94a3b8; padding:2px 8px; border-radius:99px; font-weight:700;">
                                            Not entered
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Save Grades</button>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">👥</div>
                    <div>No students in this classroom.</div>
                </div>
            @endif
        </div>
    </form>
@else
    <div class="table-card">
        <div class="empty-state">
            <div class="empty-icon">📝</div>
            <div style="font-size:14px; font-weight:600; color:#374151; margin-bottom:6px;">Select filters above</div>
            <div>Choose a subject, classroom, and exam type to start entering grades.</div>
        </div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const maxScoreInput = document.getElementById('maxScore');
    const scoreInputs   = document.querySelectorAll('.score-input');

    function validateScores() {
        const max = parseFloat(maxScoreInput?.value) || 0;
        scoreInputs.forEach(input => {
            const val = parseFloat(input.value);
            if (input.value !== '' && val > max) {
                input.classList.add('invalid');
            } else {
                input.classList.remove('invalid');
            }
        });
    }

    maxScoreInput?.addEventListener('input', validateScores);
    scoreInputs.forEach(i => i.addEventListener('input', validateScores));

    document.getElementById('gradeForm')?.addEventListener('submit', function(e) {
        const max = parseFloat(maxScoreInput?.value) || 0;
        let invalid = false;
        scoreInputs.forEach(input => {
            if (input.value !== '' && parseFloat(input.value) > max) {
                invalid = true;
            }
        });
        if (invalid) {
            e.preventDefault();
            alert('Some scores exceed the max score. Please correct before saving.');
        }
    });
});
</script>
</x-layouts.app>
