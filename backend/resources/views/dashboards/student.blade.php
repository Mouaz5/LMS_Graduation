<x-layouts.app pageTitle="Student Dashboard">
    <style>
        .welcome-banner {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
            border-radius: 16px;
            padding: 28px 32px;
            color: white;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        @media (max-width: 640px) { .cards-grid { grid-template-columns: 1fr; } }
        .dash-card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .card-hdr { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .card-ico { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .card-ttl { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #0f172a; }
        .card-sub { font-size: 12px; color: #94a3b8; }
        .grade-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f8fafc; }
        .grade-row:last-child { border-bottom: none; }
        .grade-badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .empty-box { text-align: center; padding: 20px; color: #cbd5e1; font-size: 13px; }
    </style>

    <div class="welcome-banner">
        <div style="position: relative; z-index: 1;">
            <div style="font-size: 13px; color: #6ee7b7; font-weight: 500; margin-bottom: 4px;">
                {{ now()->format('l, F j, Y') }}
            </div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; margin-bottom: 6px;">
                Hello, {{ explode(' ', auth()->user()->name)[0] }}!
            </h2>
            <p style="font-size: 14px; color: #a7f3d0;">Keep up the great work. Your learning journey continues today.</p>
        </div>
    </div>

    <div class="cards-grid">
        {{-- My Grades --}}
        <div class="dash-card">
            <div class="card-hdr">
                <div class="card-ico" style="background: #fffbeb;">
                    <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <div><div class="card-ttl">My Grades</div><div class="card-sub">Latest academic results</div></div>
            </div>
            @foreach([['Math', 'A', '#ecfdf5', '#065f46'], ['Physics', 'B+', '#eff6ff', '#1d4ed8'], ['History', 'A-', '#ecfdf5', '#065f46'], ['English', 'B', '#eff6ff', '#1d4ed8']] as [$sub, $grade, $bg, $color])
            <div class="grade-row">
                <span style="font-size: 13.5px; color: #374151; font-weight: 500;">{{ $sub }}</span>
                <span class="grade-badge" style="background: {{ $bg }}; color: {{ $color }};">{{ $grade }}</span>
            </div>
            @endforeach
        </div>

        {{-- Schedule --}}
        <div class="dash-card">
            <div class="card-hdr">
                <div class="card-ico" style="background: #eef2ff;">
                    <svg width="20" height="20" fill="none" stroke="#4F46E5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div><div class="card-ttl">My Schedule</div><div class="card-sub">Today's classes</div></div>
            </div>
            @foreach(['08:00 — Mathematics', '10:00 — Physics', '12:00 — English', '14:00 — History'] as $i => $cls)
            @php $dots = ['#4F46E5','#10b981','#3b82f6','#f59e0b']; @endphp
            <div style="display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f8fafc; font-size: 13.5px; color: #374151;">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $dots[$i] }}; flex-shrink: 0;"></div>
                {{ $cls }}
            </div>
            @endforeach
        </div>

        {{-- Announcements --}}
        <div class="dash-card">
            <div class="card-hdr">
                <div class="card-ico" style="background: #faf5ff;">
                    <svg width="20" height="20" fill="none" stroke="#9333ea" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <div><div class="card-ttl">Announcements</div><div class="card-sub">School notices</div></div>
            </div>
            <div class="empty-box">
                <svg width="32" height="32" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                No announcements yet
            </div>
        </div>

        {{-- Attendance --}}
        <div class="dash-card">
            <div class="card-hdr">
                <div class="card-ico" style="background: #ecfdf5;">
                    <svg width="20" height="20" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><div class="card-ttl">Attendance</div><div class="card-sub">This month's record</div></div>
            </div>
            <div style="display: flex; justify-content: space-around; padding: 16px 0; text-align: center;">
                @foreach([['Present', '18', '#ecfdf5', '#065f46'], ['Absent', '2', '#fef2f2', '#991b1b'], ['Late', '1', '#fffbeb', '#92400e']] as [$lbl, $val, $bg, $clr])
                <div>
                    <div style="width: 52px; height: 52px; border-radius: 50%; background: {{ $bg }}; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; font-size: 18px; font-weight: 700; color: {{ $clr }};">{{ $val }}</div>
                    <div style="font-size: 11px; color: #94a3b8; font-weight: 500;">{{ $lbl }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
