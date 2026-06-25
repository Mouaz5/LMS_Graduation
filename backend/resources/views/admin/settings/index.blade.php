<x-layouts.app :pageTitle="__('Settings')">
    <style>
        .settings-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 20px;
            text-align: center;
        }
        .settings-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }
        .settings-icon svg {
            width: 36px;
            height: 36px;
            color: #4F46E5;
        }
        .settings-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .settings-desc {
            font-size: 15px;
            color: #64748b;
            max-width: 400px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .coming-soon-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 20px;
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            color: #92400e;
        }
        .coming-soon-badge svg {
            width: 16px;
            height: 16px;
        }
    </style>

    <div class="settings-container">
        <div class="settings-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <circle cx="12" cy="12" r="3" stroke-width="2"/>
            </svg>
        </div>
        <div class="settings-title">{{ __("Settings") }}</div>
        <div class="settings-desc">
            {{ __("System settings and configuration options will be available here. This section is under development.") }}
        </div>
        <div class="coming-soon-badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ __("Coming Soon") }}
        </div>
    </div>
</x-layouts.app>
