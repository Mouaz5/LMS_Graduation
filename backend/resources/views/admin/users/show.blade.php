<x-layouts.app pageTitle="User Details">
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
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-avatar {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }
        .detail-name {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }
        .detail-email {
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
        a.related-item {
            text-decoration: none;
            color: inherit;
            transition: background 0.15s;
        }
        a.related-item:hover { background: #f8faff; }
        a.related-item:hover .related-item-chevron { opacity: 1; }
        .related-item-chevron {
            margin-left: auto;
            opacity: 0;
            color: #94a3b8;
            transition: opacity 0.15s;
        }
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

    <a href="{{ route('admin.users.index') }}" class="back-link">
        &larr; Back to Users
    </a>

    @php
        $roleBadge = [
            'admin'   => ['label' => 'Admin',   'bg' => '#eef2ff', 'color' => '#4338ca', 'dot' => '#4F46E5'],
            'teacher' => ['label' => 'Teacher', 'bg' => '#eff6ff', 'color' => '#1d4ed8', 'dot' => '#3b82f6'],
            'student' => ['label' => 'Student', 'bg' => '#ecfdf5', 'color' => '#065f46', 'dot' => '#10b981'],
            'parent'  => ['label' => 'Parent',  'bg' => '#faf5ff', 'color' => '#6b21a8', 'dot' => '#9333ea'],
        ];
        $rb = $roleBadge[$user->role] ?? $roleBadge['student'];
        $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0]))->take(2)->join('');
        $avatarColors = [
            'admin'   => ['from' => '#818cf8', 'to' => '#4F46E5'],
            'teacher' => ['from' => '#60a5fa', 'to' => '#2563eb'],
            'student' => ['from' => '#34d399', 'to' => '#059669'],
            'parent'  => ['from' => '#c084fc', 'to' => '#7c3aed'],
        ];
        $ac = $avatarColors[$user->role] ?? $avatarColors['student'];
    @endphp

    <div class="detail-card">
        <div class="detail-header">
            <div class="detail-avatar" style="background: linear-gradient(135deg, {{ $ac['from'] }}, {{ $ac['to'] }});">
                {{ $initials }}
            </div>
            <div>
                <div class="detail-name">{{ $user->name }}</div>
                <div class="detail-email">{{ $user->email }}</div>
            </div>
        </div>
        <div class="detail-body">
            <div class="detail-row">
                <span class="detail-label">Role</span>
                <span class="badge" style="background: {{ $rb['bg'] }}; color: {{ $rb['color'] }};">
                    <span class="badge-dot" style="background: {{ $rb['dot'] }};"></span>
                    {{ $rb['label'] }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone</span>
                <span class="detail-value">{{ $user->phone ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                @if($user->is_active)
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
            <div class="detail-row">
                <span class="detail-label">Joined</span>
                <span class="detail-value">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email Verified</span>
                <span class="detail-value">{{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'Not verified' }}</span>
            </div>
        </div>
    </div>

    {{-- Student Profile --}}
    @if($user->role === 'student' && $user->studentProfile)
        <div class="related-section">
            <div class="related-title">Classroom</div>
            <div class="related-card">
                <a href="{{ route('classrooms.show', $user->studentProfile->classroom) }}" class="related-item">
                    <div class="related-item-icon" style="background: #eff6ff;">
                        <svg width="16" height="16" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div style="font-weight: 600;">{{ $user->studentProfile->classroom->name }}</div>
                        <div style="font-size: 12px; color: #94a3b8;">{{ $user->studentProfile->classroom->grade->name }} — Enrolled {{ $user->studentProfile->enrollment_date->format('M d, Y') }}</div>
                    </div>
                    <span class="related-item-chevron">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </a>
            </div>
        </div>
    @endif

    {{-- Parent Links --}}
    @if($user->role === 'student' && $user->parents->count() > 0)
        <div class="related-section">
            <div class="related-title">Parents</div>
            <div class="related-card">
                @foreach($user->parents as $parent)
                    <div class="related-item">
                        <div class="related-item-icon" style="background: #faf5ff;">
                            <svg width="16" height="16" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div style="font-weight: 600;">{{ $parent->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ ucfirst($parent->pivot->relation) }} — {{ $parent->email }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Children (for parent role) --}}
    @if($user->role === 'parent' && $user->children->count() > 0)
        <div class="related-section">
            <div class="related-title">Children</div>
            <div class="related-card">
                @foreach($user->children as $child)
                    <div class="related-item">
                        <div class="related-item-icon" style="background: #ecfdf5;">
                            <svg width="16" height="16" fill="none" stroke="#059669" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21a12.083 12.083 0 01-6.16-10.422L12 14z"/></svg>
                        </div>
                        <div>
                            <div style="font-weight: 600;">{{ $child->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ ucfirst($child->pivot->relation) }} — {{ $child->email }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Teacher Assignments --}}
    @if($user->role === 'teacher' && $user->teacherAssignments->count() > 0)
        <div class="related-section">
            <div class="related-title">Subject Assignments</div>
            <div class="related-card">
                @foreach($user->teacherAssignments->load(['subject', 'classroom.grade', 'academicYear'] as $assignment)
                    <div class="related-item">
                        <div class="related-item-icon" style="background: #eff6ff;">
                            <svg width="16" height="16" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <div style="font-weight: 600;">{{ $assignment->subject->name }} — {{ $assignment->classroom->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $assignment->classroom->grade->name }} — {{ $assignment->academicYear->name }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.app>
