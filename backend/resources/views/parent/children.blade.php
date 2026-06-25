<x-layouts.app :pageTitle="__('My Children')">
    <style>
        .page-header { margin-bottom: 24px; }
        .page-header h2 { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .page-header p { font-size: 13px; color: #64748b; }
        .children-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; }
        .child-card {
            background: white; border-radius: 14px; padding: 24px;
            border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: all 0.2s;
        }
        .child-card:hover { border-color: #c7d2fe; box-shadow: 0 4px 14px rgba(79,70,229,0.08); }
        .child-header { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; }
        .child-avatar {
            width: 48px; height: 48px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 700; color: white; flex-shrink: 0;
        }
        .child-name { font-size: 16px; font-weight: 700; color: #0f172a; }
        .child-class { font-size: 13px; color: #64748b; margin-top: 2px; }
        .child-details { display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px; }
        .detail-row {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: #64748b;
        }
        .detail-row svg { width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0; }
        .detail-label { color: #94a3b8; min-width: 80px; }
        .detail-value { color: #374151; font-weight: 500; }
        .child-actions { display: flex; gap: 8px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
        .btn-sm {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 8px; font-size: 12.5px;
            font-weight: 600; text-decoration: none; transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-schedule {
            background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;
        }
        .btn-schedule:hover { background: #4338ca; color: white; }
        .btn-schedule svg { width: 14px; height: 14px; }
        .empty-state {
            text-align: center; padding: 80px 20px; color: #cbd5e1;
            background: white; border-radius: 14px; border: 1px solid #f1f5f9;
        }
        .empty-state svg { margin-bottom: 16px; }
        .empty-state h3 { font-family: 'Playfair Display', serif; font-size: 18px; color: #94a3b8; margin-bottom: 8px; }
        .empty-state p { font-size: 13px; color: #cbd5e1; }
    </style>

    <div class="page-header">
        <h2>{{ __("My Children") }}</h2>
        <p>{{ __("View your children's information and academic details") }}</p>
    </div>

    @if($children->count() > 0)
        <div class="children-grid">
            @foreach($children as $i => $child)
                @php
                    $initials = collect(explode(' ', $child->name))->map(fn($w) => strtoupper($w[0]))->take(2)->join('');
                    $gradients = [
                        'linear-gradient(135deg, #c084fc, #7c3aed)',
                        'linear-gradient(135deg, #f9a8d4, #ec4899)',
                        'linear-gradient(135deg, #93c5fd, #3b82f6)',
                        'linear-gradient(135deg, #6ee7b7, #10b981)',
                    ];
                    $profile = $child->studentProfile;
                @endphp
                <div class="child-card">
                    <div class="child-header">
                        <div class="child-avatar" style="background: {{ $gradients[$i % count($gradients)] }};">{{ $initials }}</div>
                        <div>
                            <div class="child-name">{{ $child->name }}</div>
                            <div class="child-class">
                                @if($profile && $profile->classroom)
                                    {{ $profile->classroom->grade->name }} &mdash; {{ $profile->classroom->name }}
                                @else
                                    {{ __("No classroom assigned") }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="child-details">
                        <div class="detail-row">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="detail-label">{{ __("Email:") }}</span>
                            <span class="detail-value">{{ $child->email }}</span>
                        </div>
                        @if($child->phone)
                        <div class="detail-row">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span class="detail-label">{{ __("Phone:") }}</span>
                            <span class="detail-value">{{ $child->phone }}</span>
                        </div>
                        @endif
                        @if($profile)
                        <div class="detail-row">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="detail-label">{{ __("Enrolled:") }}</span>
                            <span class="detail-value">{{ $profile->enrollment_date?->format('M d, Y') ?? __('N/A') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="child-actions">
                        <a href="{{ route('parent.child-schedule', $child) }}" class="btn-sm btn-schedule">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ __("View Schedule") }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <svg width="56" height="56" fill="none" stroke="#e2e8f0" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <h3>{{ __("No Children Linked") }}</h3>
            <p>{{ __("Your account doesn't have any children linked yet. Please contact the school administration.") }}</p>
        </div>
    @endif
</x-layouts.app>
