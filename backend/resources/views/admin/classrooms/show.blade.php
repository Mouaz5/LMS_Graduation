<x-layouts.app pageTitle="Classroom Details">
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
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
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
        .empty-msg {
            padding: 24px;
            text-align: center;
            color: #cbd5e1;
            font-size: 13px;
        }
    </style>

    <a href="{{ route('classrooms.index') }}" class="back-link">
        &larr; Back to Classrooms
    </a>

    <div class="detail-card">
        <div class="detail-header">
            <div class="detail-icon">
                <svg width="24" height="24" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <div class="detail-title">{{ $classroom->name }}</div>
                <div class="detail-subtitle">{{ $classroom->grade->name }}</div>
            </div>
        </div>
        <div class="detail-body">
            <div class="detail-row">
                <span class="detail-label">Grade</span>
                <span class="detail-value">{{ $classroom->grade->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Students</span>
                <span class="badge" style="background: #ecfdf5; color: #065f46;">
                    {{ $classroom->studentProfiles->count() }} / {{ $classroom->capacity }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Capacity</span>
                <span class="detail-value">{{ $classroom->capacity }}</span>
            </div>
        </div>
    </div>

    {{-- Students --}}
    <div class="related-section">
        <div class="related-title">Students ({{ $classroom->studentProfiles->count() }})</div>
        <div class="related-card">
            @forelse($classroom->studentProfiles as $profile)
                <div class="related-item">
                    <div class="related-item-icon" style="background: #ecfdf5;">
                        <svg width="16" height="16" fill="none" stroke="#059669" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422c-.523.28-1.12.422-1.735.422H7.575c-.615 0-1.212-.142-1.735-.422L12 14z"/></svg>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600;">{{ $profile->student->name }}</div>
                        <div style="font-size: 12px; color: #94a3b8;">{{ $profile->student->email }}</div>
                    </div>
                    <span style="font-size: 11px; color: #94a3b8;">Enrolled {{ $profile->enrollment_date->format('M d, Y') }}</span>
                </div>
            @empty
                <div class="empty-msg">No students enrolled yet.</div>
            @endforelse
        </div>
    </div>

    {{-- Teacher Assignments --}}
    @if($classroom->teacherAssignments->count() > 0)
        <div class="related-section">
            <div class="related-title">Subject Teachers</div>
            <div class="related-card">
                @foreach($classroom->teacherAssignments->groupBy('subject.name') as $subjectName => $assignments)
                    @foreach($assignments as $assignment)
                        <div class="related-item">
                            <div class="related-item-icon" style="background: #eef2ff;">
                                <svg width="16" height="16" fill="none" stroke="#4F46E5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600;">{{ $assignment->subject->name }}</div>
                                <div style="font-size: 12px; color: #94a3b8;">{{ $assignment->teacher->name }}</div>
                            </div>
                            <span class="badge" style="background: #eef2ff; color: #4338ca; font-size: 10px;">{{ $assignment->academicYear->name }}</span>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.app>
