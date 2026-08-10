<x-layouts.app :pageTitle="__('Knowledge Map')">
<style>
    .page-title { font-family: var(--font-display); font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }

    .filter-card {
        background: white; border-radius: 14px; border: 1px solid var(--border-soft);
        box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 18px 20px; margin-bottom: 20px;
        display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;
    }
    .filter-select {
        padding: 9px 14px; border: 1.5px solid var(--border); border-radius: 8px;
        font-size: 13.5px; font-family: var(--font-body); color: var(--text-strong);
        background: var(--surface-3); outline: none; min-width: 200px;
    }

    .card { background: white; border-radius: 14px; border: 1px solid var(--border-soft); box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 24px; }

    /* Tree styles */
    .tree { font-size: 13px; }
    .tree ul { list-style: none; padding-inline-start: 24px; margin: 0; }
    .tree > ul { padding-inline-start: 0; }
    .tree-node { margin: 6px 0; }
    .node-row {
        display: flex; align-items: center; gap: 10px; cursor: pointer;
        padding: 8px 12px; border-radius: 8px; transition: background 0.15s;
        border: 1px solid transparent;
    }
    .node-row:hover { background: var(--surface-2); border-color: var(--border); }
    .node-circle {
        width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0;
    }
    .mastery-green  { background: var(--success-tint); color: var(--success-text); }
    .mastery-yellow { background: var(--warning-tint); color: #854d0e; }
    .mastery-red    { background: var(--danger-tint); color: var(--danger-text); }
    .mastery-grey   { background: var(--border-soft); color: var(--text-muted); }
    .node-name { font-weight: 600; color: var(--text-primary); flex: 1; }
    .node-desc { font-size: 11px; color: var(--text-muted); }
    .node-toggle { color: var(--text-muted); font-size: 11px; transition: transform 0.2s; }
    .node-toggle.open { transform: rotate(90deg); }
    .node-children { display: none; }
    .node-children.open { display: block; }
    .legend { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; font-size: 12px; }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-dot { width: 14px; height: 14px; border-radius: 50%; }
    .empty-state { padding: 60px 20px; text-align: center; color: var(--text-muted); }
</style>

<div class="page-title">{{ __('Knowledge Map') }}</div>
<div class="page-desc">{{ __('View student mastery across learning objectives.') }}</div>

<form method="GET" action="{{ request()->is('teacher/*') ? route('teacher.diagnostic.knowledge-map') : route('admin.diagnostic.knowledge-map') }}">
    <div class="filter-card">
        <div class="filter-group">
            <label class="filter-label">{{ __('Subject') }}</label>
            <select class="filter-select" name="subject_id" data-auto-submit>
                <option value="">-- {{ __('Select Subject') }} --</option>
                @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ $subject?->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">{{ __('Student') }}</label>
            <select class="filter-select" name="student_id" data-auto-submit>
                <option value="">-- {{ __('Select Student') }} --</option>
                @foreach($students as $st)
                    <option value="{{ $st->id }}" {{ $student?->id == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>

@if($subject && $student)
    <div class="legend">
        <div class="legend-item"><div class="legend-dot" style="background:var(--success-tint); border:1px solid #bbf7d0;"></div> {{ __('Mastered (≥70%)') }}</div>
        <div class="legend-item"><div class="legend-dot" style="background:var(--warning-tint); border:1px solid #fde68a;"></div> {{ __('Developing (40–69%)') }}</div>
        <div class="legend-item"><div class="legend-dot" style="background:var(--danger-tint); border:1px solid var(--danger-border);"></div> {{ __('Needs Work (&lt;40%)') }}</div>
        <div class="legend-item"><div class="legend-dot" style="background:var(--border-soft); border:1px solid var(--border);"></div> {{ __('Not Assessed') }}</div>
    </div>

    <div class="card">
        @if(count($tree) > 0)
            <div class="tree">
                <ul>
                    @foreach($tree as $node)
                        @include('components.diagnostic.tree-node', ['node' => $node])
                    @endforeach
                </ul>
            </div>
        @else
            <x-empty-state
                :description="__('No learning objectives defined for :name.', ['name' => $subject->name])"
            />
            @if(auth()->user()->role->value === 'admin')
                <a href="{{ route('admin.diagnostic.test-builder', ['subject_id' => $subject->id]) }}" style="color:var(--primary); font-weight:600;">{{ __('Go to Test Builder') }} →</a>
            @endif
        @endif
    </div>
@elseif($subject || $student)
    <div class="card">
        <x-empty-state :description="__('Select both a subject and a student to view the knowledge map.')" />
    </div>
@endif

</x-layouts.app>
