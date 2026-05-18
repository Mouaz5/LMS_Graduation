<x-layouts.app pageTitle="Teacher Dashboard">
    <style>
        .welcome-banner {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
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
        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -60px; right: 80px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
        }
        .placeholder-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        @media (max-width: 640px) { .placeholder-grid { grid-template-columns: 1fr; } }
        .placeholder-card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .card-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .card-subtitle { font-size: 12px; color: #94a3b8; }
        .placeholder-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #f8fafc;
            font-size: 13.5px;
            color: #374151;
        }
        .placeholder-item:last-child { border-bottom: none; }
        .placeholder-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .empty-state {
            text-align: center;
            padding: 20px;
            color: #cbd5e1;
            font-size: 13px;
        }
    </style>

    <div class="welcome-banner">
        <div style="position: relative; z-index: 1;">
            <div style="font-size: 13px; color: #a5b4fc; font-weight: 500; margin-bottom: 4px;">
                {{ now()->format('l, F j, Y') }}
            </div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; margin-bottom: 6px;">
                Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', auth()->user()->name)[0] }}!
            </h2>
            <p style="font-size: 14px; color: #c7d2fe;">
                You have a full day ahead. Here's your teaching overview.
            </p>
        </div>
    </div>

    <div class="placeholder-grid">
        {{-- My Classes --}}
        <div class="placeholder-card">
            <div class="card-header">
                <div class="card-icon" style="background: #eff6ff;">
                    <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">My Classes</div>
                    <div class="card-subtitle">Currently assigned classes</div>
                </div>
            </div>
            <div class="empty-state">
                <svg width="32" height="32" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Classes will appear here once assigned
            </div>
        </div>

        {{-- My Schedule --}}
        <div class="placeholder-card">
            <div class="card-header">
                <div class="card-icon" style="background: #ecfdf5;">
                    <svg width="20" height="20" fill="none" stroke="#10b981" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">My Schedule</div>
                    <div class="card-subtitle">Today's timetable</div>
                </div>
            </div>
            @foreach(['08:00 — Mathematics 10A', '10:00 — Physics 11B', '13:00 — Mathematics 9C', '15:00 — Office Hours'] as $i => $slot)
                @php $colors = ['#4F46E5','#10b981','#3b82f6','#9333ea']; @endphp
                <div class="placeholder-item">
                    <div class="placeholder-dot" style="background: {{ $colors[$i] }};"></div>
                    {{ $slot }}
                </div>
            @endforeach
        </div>

        {{-- Attendance Entry --}}
        <div class="placeholder-card">
            <div class="card-header">
                <div class="card-icon" style="background: #eef2ff;">
                    <svg width="20" height="20" fill="none" stroke="#4F46E5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Attendance Entry</div>
                    <div class="card-subtitle">Mark today's attendance</div>
                </div>
            </div>
            <div class="empty-state">
                <svg width="32" height="32" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Attendance module coming soon
            </div>
        </div>

        {{-- My Grades --}}
        <div class="placeholder-card">
            <div class="card-header">
                <div class="card-icon" style="background: #fffbeb;">
                    <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Grade Entry</div>
                    <div class="card-subtitle">Enter and manage grades</div>
                </div>
            </div>
            <div class="empty-state">
                <svg width="32" height="32" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Grade entry module coming soon
            </div>
        </div>
    </div>
</x-layouts.app>
