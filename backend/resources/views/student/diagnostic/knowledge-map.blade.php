<x-layouts.app pageTitle="My Knowledge Map">
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

    .btn { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: background 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-primary { background: #4F46E5; color: white; }
    .btn-primary:hover { background: #3730a3; }

    .card { background: white; border-radius: 14px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 24px; }

    /* Summary bar */
    .summary-bar {
        display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px;
    }
    .summary-stat { background: #f8fafc; border-radius: 12px; padding: 14px 20px; flex: 1; min-width: 120px; text-align: center; }
    .stat-value { font-size: 24px; font-weight: 800; color: #4F46E5; }
    .stat-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; margin-top: 2px; }

    /* Tree */
    .tree { font-size: 13px; }
    .tree ul { list-style: none; padding-left: 24px; margin: 0; }
    .tree > ul { padding-left: 0; }
    .tree-node { margin: 6px 0; }
    .node-row {
        display: flex; align-items: center; gap: 10px; cursor: pointer;
        padding: 10px 14px; border-radius: 10px; transition: background 0.15s;
        border: 1px solid transparent;
    }
    .node-row:hover { background: #f8fafc; border-color: #e2e8f0; }
    .node-circle {
        width: 48px; height: 48px; border-radius: 50%; display: flex; flex-direction: column;
        align-items: center; justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0;
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
    .alert-success { background: #dcfce7; color: #166534; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
</style>

<div class="page-title">My Knowledge Map</div>
<div class="page-desc">See your mastery level across learning objectives.</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<form method="GET" action="{{ route('student.diagnostic.knowledge-map') }}">
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
            <div style="margin-left:auto; display:flex; align-items:flex-end;">
                <a href="{{ route('student.diagnostic.test', ['subject_id' => $subject->id]) }}" class="btn btn-primary">
                    Take Diagnostic Test
                </a>
            </div>
        @endif
    </div>
</form>

@if($subject)
    @php
        $flatTree = collect();
        $flatten = function($nodes) use (&$flatten, &$flatTree) {
            foreach ($nodes as $n) {
                $flatTree->push($n);
                if (!empty($n['children'])) $flatten($n['children']);
            }
        };
        $flatten($tree);
        $assessed   = $flatTree->filter(fn($n) => $n['mastery_percent'] !== null);
        $mastered   = $assessed->filter(fn($n) => $n['level'] === \App\Domain\MasteryLevel::Mastered)->count();
        $developing = $assessed->filter(fn($n) => $n['level'] === \App\Domain\MasteryLevel::Developing)->count();
        $needsWork  = $assessed->filter(fn($n) => $n['level'] === \App\Domain\MasteryLevel::NeedsWork)->count();
        $avgMastery = $assessed->isNotEmpty() ? round($assessed->avg('mastery_percent'), 1) : null;
    @endphp

    @if($assessed->isNotEmpty())
        <div class="summary-bar">
            <div class="summary-stat">
                <div class="stat-value" style="color:#166534;">{{ $mastered }}</div>
                <div class="stat-label">Mastered</div>
            </div>
            <div class="summary-stat">
                <div class="stat-value" style="color:#854d0e;">{{ $developing }}</div>
                <div class="stat-label">Developing</div>
            </div>
            <div class="summary-stat">
                <div class="stat-value" style="color:#991b1b;">{{ $needsWork }}</div>
                <div class="stat-label">Needs Work</div>
            </div>
            <div class="summary-stat">
                <div class="stat-value">{{ $avgMastery }}%</div>
                <div class="stat-label">Overall Avg</div>
            </div>
        </div>
    @endif

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
                        @include('components.diagnostic.tree-node', ['node' => $node])
                    @endforeach
                </ul>
            </div>
        @else
            <div class="empty-state">
                <div style="font-size:48px; margin-bottom:12px;">🗺️</div>
                <div style="font-size:15px; font-weight:700; color:#374151; margin-bottom:8px;">No objectives defined yet</div>
                <div>Your teacher hasn't set up learning objectives for {{ $subject->name }} yet.</div>
            </div>
        @endif
    </div>
@endif

</x-layouts.app>
