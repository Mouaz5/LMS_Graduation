<x-layouts.app pageTitle="Diagnostic Test">
<style>
    .page-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }

    .filter-card {
        background: white; border-radius: 14px; border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 18px 20px; margin-bottom: 20px;
        display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;
    }
    .filter-select {
        padding: 9px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13.5px; font-family: 'DM Sans', sans-serif; color: #374151;
        background: #fafafa; outline: none; min-width: 220px;
    }

    .card { background: white; border-radius: 14px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .card-header { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 14px; font-weight: 700; color: #0f172a; }
    .card-body { padding: 20px; }

    .btn { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: background 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-primary { background: #4F46E5; color: white; }
    .btn-primary:hover { background: #3730a3; }
    .btn-outline { background: white; color: #4F46E5; border: 1.5px solid #4F46E5; }
    .btn-outline:hover { background: #ede9fe; }

    /* Progress bar */
    .progress-wrap { background: #f1f5f9; border-radius: 99px; height: 8px; overflow: hidden; margin-bottom: 20px; }
    .progress-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #4F46E5, #818cf8); transition: width 0.4s ease; }
    .progress-label { font-size: 12px; color: #64748b; margin-bottom: 6px; display: flex; justify-content: space-between; }

    /* Question card */
    .question-card { margin-bottom: 24px; padding: 20px; background: #fafafa; border-radius: 12px; border: 1px solid #e2e8f0; }
    .question-number { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 8px; }
    .question-text { font-size: 14px; font-weight: 600; color: #0f172a; margin-bottom: 14px; line-height: 1.5; }
    .options-list { display: flex; flex-direction: column; gap: 8px; }
    .option-label {
        display: flex; align-items: center; gap: 10px; padding: 10px 14px;
        border-radius: 8px; border: 1.5px solid #e2e8f0; cursor: pointer;
        transition: border-color 0.15s, background 0.15s; font-size: 13px; color: #374151;
    }
    .option-label:has(input:checked) { border-color: #4F46E5; background: #ede9fe; color: #3730a3; font-weight: 600; }
    .option-label input[type=radio] { accent-color: #4F46E5; width: 16px; height: 16px; }

    .empty-state { padding: 60px 20px; text-align: center; color: #94a3b8; }
    .alert-success { background: #dcfce7; color: #166534; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
</style>

<div class="page-title">Diagnostic Test</div>
<div class="page-desc">Test your knowledge and discover learning gaps.</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- Subject selector --}}
<form method="GET" action="{{ route('student.diagnostic.test') }}">
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
        @if($subject)
            <div style="margin-left: auto; display: flex; gap: 10px; align-items: flex-end;">
                <form method="POST" action="{{ route('student.diagnostic.start') }}">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                    <button type="submit" class="btn btn-outline" onclick="return confirm('Start a new test? Any in-progress test will end.')">
                        Start New Test
                    </button>
                </form>
                <a href="{{ route('student.diagnostic.knowledge-map', ['subject_id' => $subject->id]) }}" class="btn btn-primary">
                    View Knowledge Map
                </a>
            </div>
        @endif
    </div>
</form>

@if($subject)
    @if($attempt && $questions->isNotEmpty())
        {{-- Progress --}}
        @php
            $totalForSubject = \App\Models\DiagnosticQuestion::where('subject_id', $subject->id)->count();
            $answered = $attempt->answers()->count();
            $pct = $totalForSubject > 0 ? round(($answered / $totalForSubject) * 100) : 0;
        @endphp
        <div class="progress-label">
            <span>Progress</span>
            <span>{{ $answered }} / {{ $totalForSubject }} questions answered</span>
        </div>
        <div class="progress-wrap">
            <div class="progress-fill" style="width: {{ $pct }}%"></div>
        </div>

        <form method="POST" action="{{ route('student.diagnostic.submit', $attempt) }}" id="testForm">
            @csrf
            @foreach($questions as $qi => $q)
                <div class="question-card">
                    <div class="question-number">Question {{ $answered + $qi + 1 }}</div>
                    <div class="question-text">{{ $q->question_text }}</div>
                    <div class="options-list">
                        @foreach($q->options as $opt)
                            <label class="option-label">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt->id }}">
                                {{ $opt->option_text }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div style="display:flex; justify-content:flex-end; margin-top: 16px;">
                <button type="submit" class="btn btn-primary" onclick="return confirmSubmit()">
                    Submit Answers & See Knowledge Map
                </button>
            </div>
        </form>

    @elseif($attempt && $questions->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div style="font-size:48px; margin-bottom:12px;">🎉</div>
                <div style="font-size:15px; font-weight:700; color:#374151; margin-bottom:8px;">All questions answered!</div>
                <div style="margin-bottom:20px;">You have completed all available questions for {{ $subject->name }}.</div>
                <a href="{{ route('student.diagnostic.knowledge-map', ['subject_id' => $subject->id]) }}" class="btn btn-primary">
                    View Your Knowledge Map
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="empty-state">
                <div style="font-size:48px; margin-bottom:12px;">📝</div>
                <div style="font-size:15px; font-weight:700; color:#374151; margin-bottom:8px;">Ready to test your knowledge?</div>
                <div style="margin-bottom:20px;">Click "Start New Test" to begin a diagnostic test for {{ $subject->name }}.</div>
                <form method="POST" action="{{ route('student.diagnostic.start') }}">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                    <button type="submit" class="btn btn-primary">Start Test</button>
                </form>
            </div>
        </div>
    @endif
@endif

<script>
function confirmSubmit() {
    const unanswered = document.querySelectorAll('.question-card').length;
    const answered = document.querySelectorAll('input[type=radio]:checked').length;
    if (answered < unanswered) {
        return confirm(`You have ${unanswered - answered} unanswered question(s). Submit anyway?`);
    }
    return true;
}
</script>
</x-layouts.app>
