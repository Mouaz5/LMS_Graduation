<x-layouts.app pageTitle="Test Builder">
<style>
    .page-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .page-desc { font-size: 13px; color: #64748b; margin-bottom: 20px; }

    .filter-card {
        background: white; border-radius: 14px; border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 18px 20px; margin-bottom: 20px;
        display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;
    }
    .filter-group { display: flex; flex-direction: column; gap: 5px; }
    .filter-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.7px; }
    .filter-select, .form-input, .form-textarea {
        padding: 9px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13.5px; font-family: 'DM Sans', sans-serif; color: #374151;
        background: #fafafa; outline: none;
    }
    .filter-select { min-width: 220px; }
    .form-input { width: 100%; }
    .form-textarea { width: 100%; min-height: 80px; resize: vertical; }
    .filter-select:focus, .form-input:focus, .form-textarea:focus {
        border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
    }

    .grid-two { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .card { background: white; border-radius: 14px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .card-header { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 14px; font-weight: 700; color: #0f172a; }
    .card-body { padding: 20px; }

    .form-label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px; display: block; }
    .form-group { margin-bottom: 14px; }
    .btn { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: background 0.2s; }
    .btn-primary { background: #4F46E5; color: white; }
    .btn-primary:hover { background: #3730a3; }
    .btn-danger { background: #fee2e2; color: #991b1b; }
    .btn-danger:hover { background: #fca5a5; }
    .btn-sm { padding: 5px 12px; font-size: 12px; }

    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #4F46E5; color: white; }
    th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 700; }
    td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
    tbody tr:nth-child(even) { background: #f8fafc; }

    .tag { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; background: #ede9fe; color: #5b21b6; }
    .empty-state { padding: 40px; text-align: center; color: #94a3b8; font-size: 13px; }

    .alert-success { background: #dcfce7; color: #166534; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
    .alert-error { background: #fee2e2; color: #991b1b; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }

    .options-list { display: flex; flex-direction: column; gap: 8px; }
    .option-row { display: flex; gap: 8px; align-items: center; }
    .option-row input[type=text] { flex: 1; }
    .option-row input[type=radio] { width: 16px; height: 16px; accent-color: #4F46E5; }
</style>

<div class="page-title">Test Builder</div>
<div class="page-desc">Create learning objectives and diagnostic questions for each subject.</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert-error">{{ $errors->first() }}</div>
@endif

{{-- Subject selector --}}
<form method="GET" action="{{ route('admin.diagnostic.test-builder') }}">
    <div class="filter-card">
        <div class="filter-group">
            <label class="filter-label">Subject</label>
            <select class="filter-select" name="subject_id" onchange="this.form.submit()">
                <option value="">-- Select Subject --</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ $subject?->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>

@if($subject)
<div class="grid-two">
    {{-- Add Learning Objective --}}
    <div class="card">
        <div class="card-header">Add Learning Objective</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.diagnostic.objectives.store') }}">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                <div class="form-group">
                    <label class="form-label">Objective Name</label>
                    <input type="text" name="name" class="form-input" required placeholder="e.g. Algebra Basics">
                </div>
                <div class="form-group">
                    <label class="form-label">Description (optional)</label>
                    <textarea name="description" class="form-textarea" placeholder="Brief description..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Parent Objective (optional)</label>
                    <select name="parent_id" class="filter-select" style="min-width: unset; width: 100%;">
                        <option value="">-- None (root) --</option>
                        @foreach($objectives as $obj)
                            <option value="{{ $obj->id }}">{{ $obj->name }}</option>
                            @foreach($obj->children as $child)
                                <option value="{{ $child->id }}">— {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Add Objective</button>
            </form>
        </div>
    </div>

    {{-- Add Question --}}
    <div class="card">
        <div class="card-header">Add Question</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.diagnostic.questions.store') }}" id="questionForm">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                <div class="form-group">
                    <label class="form-label">Learning Objective</label>
                    <select name="learning_objective_id" class="filter-select" style="min-width: unset; width: 100%;" required>
                        <option value="">-- Select Objective --</option>
                        @foreach($objectives as $obj)
                            <option value="{{ $obj->id }}">{{ $obj->name }}</option>
                            @foreach($obj->children as $child)
                                <option value="{{ $child->id }}">— {{ $child->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Question Text</label>
                    <textarea name="question_text" class="form-textarea" required placeholder="Enter the question..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Question Type</label>
                    <select name="type" class="filter-select" style="min-width: unset; width: 100%;" onchange="toggleOptions(this.value)">
                        <option value="mcq">Multiple Choice (MCQ)</option>
                        <option value="true_false">True / False</option>
                    </select>
                </div>
                <div id="optionsSection" class="form-group">
                    <label class="form-label">Options <span style="font-size:11px; color:#94a3b8;">(select the correct one)</span></label>
                    <div class="options-list" id="optionsList">
                        <div class="option-row">
                            <input type="radio" name="correct_option" value="0" checked>
                            <input type="text" name="options[0][option_text]" class="form-input" placeholder="Option A" required>
                        </div>
                        <div class="option-row">
                            <input type="radio" name="correct_option" value="1">
                            <input type="text" name="options[1][option_text]" class="form-input" placeholder="Option B" required>
                        </div>
                        <div class="option-row">
                            <input type="radio" name="correct_option" value="2">
                            <input type="text" name="options[2][option_text]" class="form-input" placeholder="Option C (optional)">
                        </div>
                        <div class="option-row">
                            <input type="radio" name="correct_option" value="3">
                            <input type="text" name="options[3][option_text]" class="form-input" placeholder="Option D (optional)">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="options[0][is_correct]" value="0">
                <input type="hidden" name="options[1][is_correct]" value="0">
                <input type="hidden" name="options[2][is_correct]" value="0">
                <input type="hidden" name="options[3][is_correct]" value="0">
                <button type="submit" class="btn btn-primary">Add Question</button>
            </form>
        </div>
    </div>
</div>

{{-- Questions Table --}}
<div class="card">
    <div class="card-header">Questions for {{ $subject->name }} ({{ $questions->count() }})</div>
    @if($questions->isEmpty())
        <div class="empty-state">No questions yet. Add your first question above.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Objective</th>
                    <th>Type</th>
                    <th>Options</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($questions as $i => $q)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td style="max-width:250px;">{{ Str::limit($q->question_text, 80) }}</td>
                        <td><span class="tag">{{ $q->learningObjective->name ?? '—' }}</span></td>
                        <td>{{ strtoupper($q->type) }}</td>
                        <td>
                            @foreach($q->options as $opt)
                                <div style="font-size:11px; {{ $opt->is_correct ? 'color:#166534; font-weight:700;' : 'color:#64748b;' }}">
                                    {{ $opt->is_correct ? '✓ ' : '' }}{{ $opt->option_text }}
                                </div>
                            @endforeach
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.diagnostic.questions.destroy', $q) }}" onsubmit="return confirm('Delete this question?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endif

<script>
function toggleOptions(type) {
    const section = document.getElementById('optionsSection');
    const list = document.getElementById('optionsList');
    if (type === 'true_false') {
        list.innerHTML = `
            <div class="option-row">
                <input type="radio" name="correct_option" value="0" checked>
                <input type="text" name="options[0][option_text]" class="form-input" value="True" required>
            </div>
            <div class="option-row">
                <input type="radio" name="correct_option" value="1">
                <input type="text" name="options[1][option_text]" class="form-input" value="False" required>
            </div>`;
    } else {
        list.innerHTML = `
            <div class="option-row">
                <input type="radio" name="correct_option" value="0" checked>
                <input type="text" name="options[0][option_text]" class="form-input" placeholder="Option A" required>
            </div>
            <div class="option-row">
                <input type="radio" name="correct_option" value="1">
                <input type="text" name="options[1][option_text]" class="form-input" placeholder="Option B" required>
            </div>
            <div class="option-row">
                <input type="radio" name="correct_option" value="2">
                <input type="text" name="options[2][option_text]" class="form-input" placeholder="Option C (optional)">
            </div>
            <div class="option-row">
                <input type="radio" name="correct_option" value="3">
                <input type="text" name="options[3][option_text]" class="form-input" placeholder="Option D (optional)">
            </div>`;
    }
}
</script>
</x-layouts.app>
