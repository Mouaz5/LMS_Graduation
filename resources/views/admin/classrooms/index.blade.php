<x-layouts.app :pageTitle="__('Classrooms')">
    <style>
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: var(--surface-2); }
        th {
            padding: 12px 16px;
            text-align: start;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        th:first-child { padding-inline-start: 20px; }
        td {
            padding: 14px 16px;
            font-size: 13.5px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-soft);
        }
        td:first-child { padding-inline-start: 20px; font-weight: 600; }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
        }
        .empty-state {
            text-align: center;
            padding: 48px;
            color: var(--text-faint);
            font-size: 14px;
        }
        .classroom-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            color: #2563eb;
            flex-shrink: 0;
        }
    </style>

    <div class="page-actions">
        <div>
            <div style="font-family: var(--font-display); font-size: 20px; font-weight: 700; color: var(--text-primary);">{{ __("Classrooms") }}</div>
            <div class="page-desc">{{ __("View all classrooms and their students") }}</div>
        </div>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>{{ __("Classroom") }}</th>
                    <th>{{ __("Grade") }}</th>
                    <th>{{ __("Students") }}</th>
                    <th>{{ __("Capacity") }}</th>
                    <th>{{ __("Subjects") }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classrooms as $classroom)
                    <tr style="cursor: pointer; transition: background 0.15s;"
                        data-row-href="{{ route('classrooms.show', $classroom) }}">
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="classroom-icon">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <span style="font-weight: 600; color: var(--text-primary);">{{ $classroom->name }}</span>
                            </div>
                        </td>
                        <td>{{ $classroom->grade->name }}</td>
                        <td>
                            <span class="badge" style="background: var(--success-tint); color: var(--success-text); border: 1px solid var(--success-border);">
                                {{ $classroom->studentProfiles->count() }}
                            </span>
                        </td>
                        <td>{{ $classroom->capacity }}</td>
                        <td>
                            @php
                                $subjects = $classroom->teacherAssignments->pluck('subject.name')->unique()->values();
                            @endphp
                            @if($subjects->count() > 0)
                                <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                    @foreach($subjects->take(3) as $subject)
                                        <span class="badge" style="background: var(--primary-tint); color: var(--primary-dark); border: 1px solid var(--primary-light); font-size: 10px;">{{ $subject }}</span>
                                    @endforeach
                                    @if($subjects->count() > 3)
                                        <span style="font-size: 11px; color: var(--text-muted);">+{{ $subjects->count() - 3 }}</span>
                                    @endif
                                </div>
                            @else
                                <span style="color: var(--text-faint);">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">{{ __("No classrooms found.") }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
