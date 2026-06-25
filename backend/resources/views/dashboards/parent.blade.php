<x-layouts.app :pageTitle="__('Parent Dashboard')">
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
            flex-shrink: 0;
        }
    </style>

    <div class="welcome-banner">
        <div style="position: relative; z-index: 1;">
            <div style="font-size: 13px; color: #ddd6fe; font-weight: 500; margin-bottom: 4px;">
                {{ now()->format('l, F j, Y') }}
            </div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; margin-bottom: 6px;">
                {{ __("Welcome, :name!", ['name' => explode(' ', $user->name)[0]]) }}
            </h2>
            <p style="font-size: 14px; color: #ede9fe;">{{ __("Track your children's academic progress in one place.") }}</p>
        </div>
    </div>

    <div class="cards-grid">
        {{-- My Children --}}
        <x-dashboard.card :title="__('My Children')" :subtitle="__(':count enrolled', ['count' => $children->count()])" iconBg="#faf5ff">
            <x-slot:icon>
                <svg width="20" height="20" fill="none" stroke="#9333ea" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </x-slot:icon>
            @forelse($children as $i => $child)
                @php
                    $initials = collect(explode(' ', $child->name))->map(fn($w) => strtoupper($w[0]))->take(2)->join('');
                    $gradients = ['linear-gradient(135deg, #c084fc, #7c3aed)', 'linear-gradient(135deg, #f9a8d4, #ec4899)', 'linear-gradient(135deg, #93c5fd, #3b82f6)', 'linear-gradient(135deg, #6ee7b7, #10b981)'];
                @endphp
                <div class="child-card">
                    <div class="child-avatar" style="background: {{ $gradients[$i % count($gradients)] }};">{{ $initials }}</div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 600; color: #0f172a;">{{ $child->name }}</div>
                        <div style="font-size: 12px; color: #94a3b8;">
                            @if($child->studentProfile)
                                {{ $child->studentProfile->classroom->grade->name ?? '' }} &mdash; {{ $child->studentProfile->classroom->name ?? '' }}
                            @else
                                {{ __("No classroom assigned") }}
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="dash-card-empty">
                    <svg width="32" height="32" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    {{ __("No children linked to your account") }}
                </div>
            @endforelse
        </x-dashboard.card>

        {{-- Recent Grades --}}
        <x-dashboard.card :title="__('Recent Grades')" :subtitle="__('Latest results')" iconBg="#fffbeb">
            <x-slot:icon>
                <svg width="20" height="20" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </x-slot:icon>
            <div class="dash-card-empty">
                <svg width="32" height="32" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                {{ __("Grades module coming soon") }}
            </div>
        </x-dashboard.card>

        {{-- Attendance Summary --}}
        <x-dashboard.card :title="__('Attendance Summary')" :subtitle="__('This month')" iconBg="#ecfdf5">
            <x-slot:icon>
                <svg width="20" height="20" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
            <div class="dash-card-empty">
                <svg width="32" height="32" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ __("Attendance module coming soon") }}
            </div>
        </x-dashboard.card>

        {{-- Fees --}}
        <x-dashboard.card :title="__('Fees Overview')" :subtitle="__('Payment status')" iconBg="#eff6ff">
            <x-slot:icon>
                <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </x-slot:icon>
            <div class="dash-card-empty">
                <svg width="32" height="32" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                {{ __("Fee management coming soon") }}
            </div>
        </x-dashboard.card>
    </div>
</x-layouts.app>
