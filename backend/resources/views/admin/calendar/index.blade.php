<x-layouts.app pageTitle="School Calendar">
    <style>
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 20px;
            background: #4F46E5;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(79,70,229,0.3);
        }
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(79,70,229,0.4);
        }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th {
            padding: 12px 16px;
            text-align: start;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        th:first-child { padding-inline-start: 20px; }
        td {
            padding: 14px 16px;
            font-size: 13.5px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        td:first-child { padding-inline-start: 20px; font-weight: 600; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-holiday { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-event { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
        .badge-exam { background: #fce7f3; color: #9d174d; border: 1px solid #f9a8d4; }
        .empty-state {
            text-align: center;
            padding: 48px;
            color: #cbd5e1;
            font-size: 14px;
        }
    </style>

    <div class="page-actions">
        <div>
            <div style="font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a;">School Calendar</div>
            <div class="page-desc">Manage holidays, events, and exam schedules</div>
        </div>
        <a href="{{ route('admin.calendar.create') }}" class="btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Event
        </a>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr style="cursor: pointer; transition: background 0.15s;"
                        onclick="window.location='{{ route('admin.calendar.show', $event) }}'"
                        onmouseover="this.style.background='#fafbff'"
                        onmouseout="this.style.background='transparent'">
                        <td>{{ $event->date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $event->type }}">
                                {{ ucfirst($event->type) }}
                            </span>
                        </td>
                        <td>{{ $event->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-state">No calendar events found. Add one to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
