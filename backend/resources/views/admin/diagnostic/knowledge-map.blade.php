<x-layouts.app pageTitle="Knowledge Map">
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
    .filter-select {
        padding: 9px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13.5px; font-family: 'DM Sans', sans-serif; color: #374151;
        background: #fafafa; outline: none; min-width: 200px;
    }
    .filter-select:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }

    .card { background: white; border-radius: 14px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 24px; }

    /* Tree styles */
    .tree { font-size: 13px; }
    .tree ul { list-style: none; padding-left: 24px; margin: 0; }
    .tree > ul { padding-left: 0; }
    .tree-node { margin: 6px 0; }
    .node-row {
        display: flex; align-items: center; gap: 10px; cursor: pointer;
        padding: 8px 12px; border-radius: 8px; transition: background 0.15s;
        border: 1px solid transparent;
    }
    .node-row:hover { background: #f8fafc; border-color: #e2e8f0; }
    .node-circle {
        width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0;
    }
    .mastery-green  { background: #dcfce7; color: #166534; }
    .mastery-yellow { background: #fef9c3; color: #854d0e; }
    .mastery-red    { background: #fee2e2; color: #991b1b; }
    .mastery-grey   { background: #f1f5f9; color: #94a3b8; }
    .node-name { font-weight: 600; color: #0f172a; flex: 1; }
    .node-desc { font-size: 11px; color: #94a3b8; }
    .node-toggle { color: #94a3b8; font-size: 11px; transition: transform 0.2s; }
    .node-toggle.open { transform: rotate(90deg); }
    .node-children { display: none; }
    .node-children.open { display: block; }
    .legend { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; font-size: 12px; }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-dot { width: 14px; height: 14px; border-radius: 50%; }
    .empty-state { padding: 60px 20px; text-align: center; color: #94a3b8; }
</style>

<div class="page-title">Knowledge Map</div>
<div class="page-desc">View student mastery across learning objectives.</div>

<form method="GET" action="{{ request()->is('teacher/*') ? route('teacher.diagnostic.knowledge-map') : route('admin.diagnostic.knowledge-map') }}">
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
        <div class="filter-group">
            <label class="filter-label">Student</label>
            <select class="filter-select" name="student_id" onchange="this.form.submit()">
                <option value="">-- Select Student --</option>
                @foreach($students as $st)
                    <option value="{{ $st->id }}" {{ $student?->id == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>

@if($subject && $student)
    <div class="legend">
        <div class="legend-item"><div class="legend-dot" style="background:#dcfce7; border:1px solid #bbf7d0;"></div> Mastered (≥70%)</div>
        <div class="legend-item"><div class="legend-dot" style="background:#fef9c3; border:1px solid #fde68a;"></div> Developing (40–69%)</div>
        <div class="legend-item"><div class="legend-dot" style="background:#fee2e2; border:1px solid #fca5a5;"></div> Needs Work (&lt;40%)</div>
        <div class="legend-item"><div class="legend-dot" style="background:#f1f5f9; border:1px solid #e2e8f0;"></div> Not Assessed</div>
    </div>

    <div class="card">
        @if(count($tree) > 0)
            <div class="tree">
                <ul>
                    @foreach($tree as $node)
                        @include('admin.diagnostic._tree-node', ['node' => $node, 'depth' => 0])
                    @endforeach
                </ul>
            </div>
        @else
            <div class="empty-state">
                No learning objectives defined for {{ $subject->name }}.
                <br><a href="{{ route('admin.diagnostic.test-builder', ['subject_id' => $subject->id]) }}" style="color:#4F46E5; font-weight:600;">Go to Test Builder →</a>
            </div>
        @endif
    </div>
@elseif($subject || $student)
    <div class="card">
        <div class="empty-state">Select both a subject and a student to view the knowledge map.</div>
    </div>
@endif

<script>
function toggleNode(id) {
    const children = document.getElementById('children-' + id);
    const toggle = document.getElementById('toggle-' + id);
    if (children) {
        children.classList.toggle('open');
        toggle.classList.toggle('open');
    }
}
</script>
</x-layouts.app>
