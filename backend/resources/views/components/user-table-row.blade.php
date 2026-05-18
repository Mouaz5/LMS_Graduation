@props(['user'])

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

<tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s; cursor: pointer;"
    onclick="window.location='{{ route('admin.users.show', $user) }}'"
    onmouseover="this.style.background='#fafbff'"
    onmouseout="this.style.background='transparent'">

    {{-- User Info --}}
    <td style="padding: 14px 20px;">
        <div style="display: flex; align-items: center; gap: 11px;">
            <div style="
                width: 36px;
                height: 36px;
                border-radius: 10px;
                background: linear-gradient(135deg, {{ $ac['from'] }}, {{ $ac['to'] }});
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 700;
                color: white;
                flex-shrink: 0;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            ">{{ $initials }}</div>
            <div>
                <div style="font-size: 14px; font-weight: 600; color: #0f172a;">{{ $user->name }}</div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 1px;">{{ $user->email }}</div>
            </div>
        </div>
    </td>

    {{-- Role --}}
    <td style="padding: 14px 16px;">
        <span style="
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            background: {{ $rb['bg'] }};
            color: {{ $rb['color'] }};
        ">
            <span style="width: 5px; height: 5px; border-radius: 50%; background: {{ $rb['dot'] }}; display: inline-block;"></span>
            {{ $rb['label'] }}
        </span>
    </td>

    {{-- Phone --}}
    <td style="padding: 14px 16px; font-size: 13px; color: #64748b;">
        {{ $user->phone ?? '—' }}
    </td>

    {{-- Status --}}
    <td style="padding: 14px 16px;">
        @if($user->is_active)
            <span style="
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 11.5px;
                font-weight: 600;
                background: #ecfdf5;
                color: #065f46;
            ">
                <span style="width: 5px; height: 5px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                Active
            </span>
        @else
            <span style="
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 11.5px;
                font-weight: 600;
                background: #f8fafc;
                color: #94a3b8;
            ">
                <span style="width: 5px; height: 5px; border-radius: 50%; background: #cbd5e1; display: inline-block;"></span>
                Inactive
            </span>
        @endif
    </td>

    {{-- Joined --}}
    <td style="padding: 14px 16px; font-size: 12px; color: #94a3b8;">
        {{ $user->created_at->format('M d, Y') }}
    </td>

    {{-- Actions --}}
    <td style="padding: 14px 20px; text-align: end;">
        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" style="display: inline;">
            @csrf
            @method('PATCH')
            <button type="submit" style="
                padding: 6px 14px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 600;
                font-family: 'DM Sans', sans-serif;
                cursor: pointer;
                transition: all 0.2s;
                border: 1px solid;
                {{ $user->is_active
                    ? 'background: #fff1f2; border-color: #fca5a5; color: #dc2626;'
                    : 'background: #ecfdf5; border-color: #a7f3d0; color: #059669;' }}
            ">
                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
    </td>
</tr>
