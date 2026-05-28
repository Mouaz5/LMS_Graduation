<x-layouts.app pageTitle="User Details">
    <style>
        .back-link {
            font-size: 13px; color: #64748b; text-decoration: none;
            display: inline-flex; align-items: center; gap: 4px; margin-bottom: 20px;
        }
        .back-link:hover { color: #334155; }
        .detail-header {
            padding: 28px; display: flex; align-items: center; gap: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .detail-avatar {
            width: 56px; height: 56px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 700; color: white; flex-shrink: 0;
        }
        .detail-name { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; }
        .detail-email { font-size: 13px; color: #94a3b8; margin-top: 2px; }
        .detail-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 28px; border-bottom: 1px solid #f8fafc;
        }
        .detail-label { font-size: 13px; font-weight: 500; color: #94a3b8; }
        .detail-value { font-size: 14px; font-weight: 600; color: #0f172a; }
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
        }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }

        /* Related sections */
        .related-section { margin-top: 24px; max-width: 640px; }
        .related-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 12px;
        }
        .related-title { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #0f172a; }
        .related-card {
            background: white; border-radius: 14px; border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;
        }
        .related-item {
            padding: 14px 20px; font-size: 13.5px; color: #334155;
            border-bottom: 1px solid #f8fafc;
            display: flex; align-items: center; gap: 10px;
        }
        .related-item:last-child { border-bottom: none; }
        .related-item-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .empty-state { padding: 24px 20px; text-align: center; color: #94a3b8; font-size: 13.5px; }

        /* Link form inside card */
        .link-form-card {
            background: white; border-radius: 14px; border: 1px dashed #c7d2fe;
            padding: 20px; margin-top: 12px; max-width: 640px;
        }
        .link-form-title { font-size: 13.5px; font-weight: 600; color: #334155; margin-bottom: 14px; }
        .link-form-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
        .link-form-row select, .link-form-row .select-wrap select {
            padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 13.5px; font-family: 'DM Sans', sans-serif; color: #0f172a;
            background: #fafafa; outline: none; transition: all 0.2s; min-width: 180px;
        }
        .link-form-row select:focus { border-color: #4F46E5; background: white; }
        .btn-link {
            padding: 9px 18px; background: #4F46E5; color: white; border: none;
            border-radius: 10px; font-size: 13px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
            white-space: nowrap;
        }
        .btn-link:hover { background: #4338ca; }
        .btn-unlink {
            margin-left: auto; padding: 4px 10px;
            background: #fff1f2; color: #e11d48;
            border: 1px solid #fecdd3; border-radius: 7px;
            font-size: 12px; font-weight: 600; cursor: pointer;
            font-family: 'DM Sans', sans-serif; transition: all 0.15s;
        }
        .btn-unlink:hover { background: #ffe4e6; }
        .relation-chip {
            font-size: 11.5px; font-weight: 600; padding: 2px 8px;
            border-radius: 5px; background: #f3f4f6; color: #6b7280;
            border: 1px solid #e5e7eb;
        }
        .alert-success {
            background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px;
            padding: 12px 16px; font-size: 13.5px; color: #065f46; margin-bottom: 16px;
        }
    </style>

    <a href="{{ route('admin.users.index') }}" class="back-link">
        &larr; Back to Users
    </a>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

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

    {{-- Core detail card --}}
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
        </div>
    </div>

    {{-- ── STUDENT: classroom + parents ─────────────────────────────── --}}
    @if($user->role === 'student')

        @if($user->studentProfile)
            <div class="related-section">
                <div class="related-title" style="margin-bottom: 12px;">Classroom</div>
                <div class="related-card">
                    <div class="related-item">
                        <div class="related-item-icon" style="background: #eff6ff;">
                            <svg width="16" height="16" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div style="font-weight: 600;">{{ $user->studentProfile->classroom->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">
                                {{ $user->studentProfile->classroom->grade->name }} —
                                Enrolled {{ $user->studentProfile->enrollment_date->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Parents list + link form --}}
        <div class="related-section">
            <div class="related-header">
                <div class="related-title">Parents ({{ $user->parents->count() }})</div>
            </div>
            <div class="related-card">
                @forelse($user->parents as $parent)
                    <div class="related-item">
                        <div class="related-item-icon" style="background: #faf5ff;">
                            <svg width="16" height="16" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600;">{{ $parent->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $parent->email }}</div>
                        </div>
                        <span class="relation-chip">{{ ucfirst($parent->pivot->relation) }}</span>
                        <form method="POST" action="{{ route('admin.users.unlink-parent', $user) }}"
                              onsubmit="return confirm('Unlink {{ $parent->name }}?')">
                            @csrf @method('DELETE')
                            <input type="hidden" name="parent_user_id" value="{{ $parent->id }}">
                            <button type="submit" class="btn-unlink">Unlink</button>
                        </form>
                    </div>
                @empty
                    <div class="empty-state">No parents linked yet.</div>
                @endforelse
            </div>

            @if($availableParents->count() > 0)
                <div class="link-form-card">
                    <div class="link-form-title">Link a parent to this student</div>
                    <form method="POST" action="{{ route('admin.users.link-parent', $user) }}">
                        @csrf
                        <div class="link-form-row">
                            <select name="parent_user_id" required>
                                <option value="">Select parent…</option>
                                @foreach($availableParents as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->email }})</option>
                                @endforeach
                            </select>
                            <select name="relation" required>
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="guardian">Guardian</option>
                            </select>
                            <button type="submit" class="btn-link">Link Parent</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

    @endif

    {{-- ── PARENT: children list + link form ────────────────────────── --}}
    @if($user->role === 'parent')

        <div class="related-section">
            <div class="related-header">
                <div class="related-title">Children ({{ $user->children->count() }})</div>
            </div>
            <div class="related-card">
                @forelse($user->children as $child)
                    <div class="related-item">
                        <div class="related-item-icon" style="background: #ecfdf5;">
                            <svg width="16" height="16" fill="none" stroke="#059669" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600;">{{ $child->name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">
                                {{ $child->email }}
                                @if($child->studentProfile?->classroom)
                                    — {{ $child->studentProfile->classroom->name }}, {{ $child->studentProfile->classroom->grade->name }}
                                @endif
                            </div>
                        </div>
                        <span class="relation-chip">{{ ucfirst($child->pivot->relation) }}</span>
                        <form method="POST" action="{{ route('admin.users.unlink-child', $user) }}"
                              onsubmit="return confirm('Unlink {{ $child->name }}?')">
                            @csrf @method('DELETE')
                            <input type="hidden" name="student_user_id" value="{{ $child->id }}">
                            <button type="submit" class="btn-unlink">Unlink</button>
                        </form>
                    </div>
                @empty
                    <div class="empty-state">No children linked yet.</div>
                @endforelse
            </div>

            @if($availableStudents->count() > 0)
                <div class="link-form-card">
                    <div class="link-form-title">Link a student to this parent</div>
                    <form method="POST" action="{{ route('admin.users.link-child', $user) }}">
                        @csrf
                        <div class="link-form-row">
                            <select name="student_user_id" required>
                                <option value="">Select student…</option>
                                @foreach($availableStudents as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
                                @endforeach
                            </select>
                            <select name="relation" required>
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="guardian">Guardian</option>
                            </select>
                            <button type="submit" class="btn-link">Link Student</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

    @endif

    {{-- ── TEACHER: subject assignments ─────────────────────────────── --}}
    @if($user->role === 'teacher' && $user->teacherAssignments->count() > 0)
        <div class="related-section">
            <div class="related-title" style="margin-bottom: 12px;">Subject Assignments</div>
            <div class="related-card">
                @foreach($user->teacherAssignments as $assignment)
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
