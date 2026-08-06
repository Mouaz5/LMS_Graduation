@php
    $currentLocale = app()->getLocale();
    // Preserve where the user is: redirect()->back() in the controller returns here.
@endphp
<div class="lang-switcher">
    <a href="{{ route('language.switch', 'en') }}"
       class="lang-option {{ $currentLocale === 'en' ? 'active' : '' }}"
       aria-label="Switch to English">EN</a>
    <span class="lang-sep">|</span>
    <a href="{{ route('language.switch', 'ar') }}"
       class="lang-option {{ $currentLocale === 'ar' ? 'active' : '' }}"
       aria-label="التبديل إلى العربية">ع</a>
</div>

@once
    <style>
        .lang-switcher {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 50px;
            background: var(--surface-2, #f8fafc);
            border: 1px solid var(--border, #e2e8f0);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
        }
        .lang-switcher .lang-option {
            color: var(--text-muted, #94a3b8);
            text-decoration: none;
            transition: color 0.2s;
            line-height: 1;
        }
        .lang-switcher .lang-option:hover { color: var(--primary, #4F46E5); }
        .lang-switcher .lang-option.active { color: var(--primary, #4F46E5); }
        .lang-switcher .lang-sep { color: var(--border, #cbd5e1); }
    </style>
@endonce
