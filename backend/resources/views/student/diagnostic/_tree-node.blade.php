@php
    $mastery = $node['mastery_percent'];
    if ($mastery === null) {
        $circleClass = 'mastery-grey';
        $label = '—';
    } elseif ($mastery >= 70) {
        $circleClass = 'mastery-green';
        $label = $mastery . '%';
    } elseif ($mastery >= 40) {
        $circleClass = 'mastery-yellow';
        $label = $mastery . '%';
    } else {
        $circleClass = 'mastery-red';
        $label = $mastery . '%';
    }
    $hasChildren = count($node['children']) > 0;
@endphp
<li class="tree-node">
    <div class="node-row" onclick="{{ $hasChildren ? 'toggleNode(' . $node['id'] . ')' : '' }}">
        @if($hasChildren)
            <span class="node-toggle" id="toggle-{{ $node['id'] }}">▶</span>
        @else
            <span style="width:16px;"></span>
        @endif
        <div class="node-circle {{ $circleClass }}">{{ $label }}</div>
        <div>
            <div class="node-name">{{ $node['name'] }}</div>
            @if($node['description'])
                <div class="node-desc">{{ $node['description'] }}</div>
            @endif
        </div>
    </div>
    @if($hasChildren)
        <div class="node-children" id="children-{{ $node['id'] }}">
            <ul>
                @foreach($node['children'] as $child)
                    @include('student.diagnostic._tree-node', ['node' => $child])
                @endforeach
            </ul>
        </div>
    @endif
</li>
