<x-layouts.app pageTitle="Academic Year Details">
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
        }
        .detail-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }
        .detail-subtitle {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 4px;
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
        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        .related-section {
            margin-top: 24px;
            max-width: 640px;
        }
        .related-title {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .related-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .related-item {
            padding: 14px 20px;
            font-size: 13.5px;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .related-item:last-child { border-bottom: none; }
        .related-item-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>

    <a href="{{ route('admin.academic-years.index') }}" class="back-link">
        &larr; Back to Academic Years
    </a>

    <div class="detail-card">
        <div class="detail-header">
            <div class="detail-title">{{ $year->name }}</div>
            <div class="detail-subtitle">{{ $year->school->name }}</div>
        </div>
        <div class="detail-body">
            <div class="detail-row">
                <span class="detail-label">Start Date</span>
                <span class="detail-value">{{ $year->start_date->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">End Date</span>
                <span class="detail-value">{{ $year->end_date->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Duration</span>
                <span class="detail-value">{{ $year->start_date->diffInMonths($year->end_date) }} months</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                @if($year->is_active)
                    <span class="badge" style="background: #ecfdf5; color: #065f46;">
                        <span class="badge-dot" style="background: #10b981;"></span>
                        Active
                    </span>
                @else
                    <span class="badge" style="background: #f8fafc; color: #94a3b8;">
                        <span class="badge-dot" style="background: #cbd5e1;"></span>
                        Inactive
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Semesters --}}
    @if($year->semesters->count() > 0)
        <div class="related-section">
            <div class="related-title">Semesters ({{ $year->semesters->count() }})</div>
            <div class="related-card">
                @foreach($year->semesters as $semester)
                    <div class="related-item">
                        <div class="related-item-icon" style="background: {{ $semester->is_active ? '#ecfdf5' : '#f8fafc' }};">
                            <svg width="16" height="16" fill="none" stroke="{{ $semester->is_active ? '#059669' : '#94a3b8' }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600;">{{ $semester->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $semester->start_date->format('M d') }} — {{ $semester->end_date->format('M d, Y') }}</div>
                        </div>
                        @if($semester->is_active)
                            <span class="badge" style="background: #ecfdf5; color: #065f46; font-size: 10px;">Active</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.app>
