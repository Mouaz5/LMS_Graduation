<x-layouts.app :pageTitle="__('Subjects')">
    <style>
        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 20px; background: #4F46E5; color: white;
            border-radius: 10px; text-decoration: none; font-size: 13.5px;
            font-weight: 600; font-family: 'DM Sans', sans-serif;
            transition: all 0.2s; box-shadow: 0 2px 8px rgba(79,70,229,0.3);
        }
        .btn-primary:hover { background: #4338ca; transform: translateY(-1px); }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th {
            padding: 12px 16px; text-align: start;
            font-size: 11px; font-weight: 700; color: #94a3b8;
            letter-spacing: 0.8px; text-transform: uppercase;
        }
        th:first-child { padding-inline-start: 20px; }
        td {
            padding: 14px 16px; font-size: 13.5px; color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        td:first-child { padding-inline-start: 20px; font-weight: 600; }
        .code-badge {
            display: inline-block; padding: 3px 10px; border-radius: 6px;
            font-size: 11.5px; font-weight: 600; font-family: monospace;
            background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
        }
        .actions { display: flex; gap: 8px; }
        .btn-edit {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 5px 12px; font-size: 12.5px; font-weight: 600;
            border-radius: 8px; text-decoration: none; transition: all 0.15s;
            color: #4F46E5; background: #eef2ff; border: 1px solid #c7d2fe;
        }
        .btn-edit:hover { background: #e0e7ff; }
        .btn-delete {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 5px 12px; font-size: 12.5px; font-weight: 600;
            border-radius: 8px; border: none; cursor: pointer; transition: all 0.15s;
            color: #dc2626; background: #fef2f2; border: 1px solid #fecaca;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-delete:hover { background: #fee2e2; }
        .empty-state { text-align: center; padding: 48px; color: #cbd5e1; font-size: 14px; }
        .alert-success {
            background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px;
            padding: 12px 16px; font-size: 13.5px; color: #065f46; margin-bottom: 16px;
        }
    </style>

    <div class="page-actions">
        <div>
            <div style="font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a;">{{ __("Subjects") }}</div>
            <div class="page-desc">{{ __("Manage school subjects") }}</div>
        </div>
        <a href="{{ route('admin.subjects.create') }}" class="btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __("New Subject") }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>{{ __("Name") }}</th>
                    <th>{{ __("Code") }}</th>
                    <th>{{ __("School") }}</th>
                    <th>{{ __("Actions") }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                    <tr style="cursor: pointer; transition: background 0.15s;"
                        onclick="window.location='{{ route('admin.subjects.show', $subject) }}'"
                        onmouseover="this.style.background='#fafbff'"
                        onmouseout="this.style.background='transparent'">
                        <td>{{ $subject->name }}</td>
                        <td><span class="code-badge">{{ $subject->code }}</span></td>
                        <td style="font-weight: 400;">{{ $subject->school?->name ?? '—' }}</td>
                        <td onclick="event.stopPropagation()">
                            <div class="actions">
                                <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn-edit">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    {{ __("Edit") }}
                                </a>
                                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                                      onsubmit="return confirm('Delete subject {{ $subject->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        {{ __("Delete") }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">{{ __("No subjects found. Create one to get started.") }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
