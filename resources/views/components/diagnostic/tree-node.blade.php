@php
    $level       = $node['level'];
    $circleClass = $level->color();
    $label       = $level->label($node['mastery_percent']);
    $hasChildren = count($node['children']) > 0;
@endphp
<li class="tree-node">
    <div class="node-row" @if($hasChildren) data-node-id="{{ $node['id'] }}" @endif>
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
                    @include('components.diagnostic.tree-node', ['node' => $child])
                @endforeach
            </ul>
        </div>
    @endif
</li>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tree').forEach(function (tree) {
        tree.addEventListener('click', function (e) {
            const row = e.target.closest('[data-node-id]');
            if (!row) return;
            const id = row.dataset.nodeId;
            const children = document.getElementById('children-' + id);
            const toggle   = document.getElementById('toggle-'   + id);
            if (children) children.classList.toggle('open');
            if (toggle)   toggle.classList.toggle('open');
        });
    });
});
</script>
@endonce
