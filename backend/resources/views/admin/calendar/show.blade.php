<x-layouts.app pageTitle="Calendar Event Details">
    <style>
        .back-link {
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 20px;
        }
        .back-link:hover { color: #334155; }
        .detail-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
            max-width: 640px;
        }
        .detail-header {
            padding: 28px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .detail-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .detail-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }
        .detail-subtitle {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .detail-body { padding: 0; }
        .detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 28px;
            border-bottom: 1px solid #f8fafc;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label {
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>

    <a href="{{ route('admin.calendar.index') }}" class="back-link">
        &larr; Back to Calendar
    </a>

    @php
        $typeConfig = [
            'holiday' => ['label' => 'Holiday', 'bg' => '#fef3c7', 'color' => '#92400e', 'iconBg' => '#fef3c7', 'iconColor' => '#d97706'],
            'event'   => ['label' => 'Event',   'bg' => '#dbeafe', 'color' => '#1e40af', 'iconBg' => '#dbeafe', 'iconColor' => '#2563eb'],
            'exam'    => ['label' => 'Exam',    'bg' => '#fce7f3', 'color' => '#9d174d', 'iconBg' => '#fce7f3', 'iconColor' => '#db2777'],
        ];
        $tc = $typeConfig[$event->type] ?? $typeConfig['event'];
    @endphp

    <div class="detail-card">
        <div class="detail-header">
            <div class="detail-icon" style="background: {{ $tc['iconBg'] }};">
                @if($event->type === 'holiday')
                    <svg width="22" height="22" fill="none" stroke="{{ $tc['iconColor'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                @elseif($event->type === 'exam')
                    <svg width="22" height="22" fill="none" stroke="{{ $tc['iconColor'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                @else
                    <svg width="22" height="22" fill="none" stroke="{{ $tc['iconColor'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                @endif
            </div>
            <div>
                <div class="detail-title">{{ $event->description }}</div>
                <div class="detail-subtitle">{{ $event->date->format('l, F j, Y') }}</div>
            </div>
        </div>
        <div class="detail-body">
            <div class="detail-row">
                <span class="detail-label">Date</span>
                <span class="detail-value">{{ $event->date->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Day</span>
                <span class="detail-value">{{ $event->date->format('l') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Type</span>
                <span class="badge" style="background: {{ $tc['bg'] }}; color: {{ $tc['color'] }};">
                    {{ $tc['label'] }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">School</span>
                <span class="detail-value">{{ $event->school->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Created</span>
                <span class="detail-value">{{ $event->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>
</x-layouts.app>
