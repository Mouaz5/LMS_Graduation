<x-layouts.app pageTitle="Parent Dashboard">
    <style>
        .welcome-banner {
            background: linear-gradient(135deg, #4c1d95 0%, #5b21b6 50%, #6d28d9 100%);
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
        .child-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 10px;
            background: #fafbff;
            border: 1px solid #f1f5f9;
            margin-bottom: 8px;
        }
        .child-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c084fc, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: white;
        }
        .grade-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f8fafc; }
        .grade-row:last-child { border-bottom: none; }
        .grade-badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .empty-box { text-align: center; padding: 20px; color: #cbd5e1; font-size: 13px; }
    </style>

    <div class="welcome-banner">
        <div style="position: relative; z-index: 1;">
            <div style="font-size: 13px; color: #ddd6fe; font-weight: 500; margin-bottom: 4px;">
                {{ now()->format('l, F j, Y') }}
            </div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; margin-bottom: 6px;">
                Welcome, {{ explode(' ', auth()->user()->name)[0] }}!
            </h2>
            <p style="font-size: 14px; color: #ede9fe;">Track your children's academic progress in one place.</p>
        </div>
    </div>

    <div class="cards-grid">
        {{-- My Children --}}
        <div class="dash-card">
            <div class="card-hdr">
                <div class="card-ico" style="background: #faf5ff;">
                    <svg width="20" height="20" fill="none" stroke="#9333ea" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div><div class="card-ttl">My Children</div><div class="card-sub">Enrolled students</div></div>
            </div>
            <div class="child-card">
                <div class="child-avatar">AH</div>
                <div>
                    <div style="font-size: 13.5px; font-weight: 600; color: #0f172a;">Ahmad Hassan</div>
                    <div style="font-size: 12px; color: #94a3b8;">Grade 10 — Section A</div>
                </div>
            </div>
            <div class="child-card">
                <div class="child-avatar" style="background: linear-gradient(135deg, #f9a8d4, #ec4899);">SH</div>
                <div>
                    <div style="font-size: 13.5px; font-weight: 600; color: #0f172a;">Sara Hassan</div>
                    <div style="font-size: 12px; color: #94a3b8;">Grade 8 — Section B</div>
                </div>
            </div>
        </div>

        {{-- Recent Grades --}}
        <div class="dash-card">
            <div class="card-hdr">
                <div class="card-ico" style="background: #fffbeb;">
                    <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <div><div class="card-ttl">Recent Grades</div><div class="card-sub">Latest results</div></div>
            </div>
            @foreach([['Ahmad — Math', 'A'], ['Ahmad — Physics', 'B+'], ['Sara — English', 'A-'], ['Sara — History', 'B']] as [$name, $grade])
            <div class="grade-row">
                <span style="font-size: 13px; color: #374151;">{{ $name }}</span>
                <span class="grade-badge" style="background: #ecfdf5; color: #065f46;">{{ $grade }}</span>
            </div>
            @endforeach
        </div>

        {{-- Attendance Summary --}}
        <div class="dash-card">
            <div class="card-hdr">
                <div class="card-ico" style="background: #ecfdf5;">
                    <svg width="20" height="20" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><div class="card-ttl">Attendance Summary</div><div class="card-sub">This month</div></div>
            </div>
            @foreach([['Ahmad', '18', '1', '0'], ['Sara', '19', '0', '1']] as [$name, $p, $a, $l])
            <div style="margin-bottom: 14px;">
                <div style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">{{ $name }}</div>
                <div style="display: flex; gap: 8px;">
                    @foreach([[$p,'Present','#ecfdf5','#065f46'], [$a,'Absent','#fef2f2','#991b1b'], [$l,'Late','#fffbeb','#92400e']] as [$v,$l2,$bg,$c])
                    <div style="flex: 1; text-align: center; background: {{ $bg }}; border-radius: 8px; padding: 8px 4px;">
                        <div style="font-size: 18px; font-weight: 700; color: {{ $c }};">{{ $v }}</div>
                        <div style="font-size: 10px; color: {{ $c }}; opacity: 0.7;">{{ $l2 }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Fees --}}
        <div class="dash-card">
            <div class="card-hdr">
                <div class="card-ico" style="background: #eff6ff;">
                    <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div><div class="card-ttl">Fees Overview</div><div class="card-sub">Payment status</div></div>
            </div>
            <div class="empty-box">
                <svg width="32" height="32" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Fee management coming soon
            </div>
        </div>
    </div>
</x-layouts.app>
