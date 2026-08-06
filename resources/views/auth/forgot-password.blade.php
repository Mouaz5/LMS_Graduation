<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Forgot Password') }} — SchoolLMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:wght@600;700&family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', 'Cairo', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            -webkit-font-smoothing: antialiased;
        }
        [dir="rtl"] body, [dir="rtl"] h1 { font-family: 'Cairo', sans-serif; }
        .card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            border: 1px solid #e2e8f0;
        }
        .icon-wrap {
            width: 56px; height: 56px;
            background: #eef2ff;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }
        .icon-wrap svg { width: 26px; height: 26px; color: #4F46E5; }
        h1 { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        .sub { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 28px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 7px; }
        input[type="email"] {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 14px; font-family: 'DM Sans', 'Cairo', sans-serif; color: #0f172a;
            background: #fafafa; outline: none; transition: all 0.2s;
        }
        input[type="email"]:focus { border-color: #4F46E5; background: white; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .error { font-size: 12px; color: #ef4444; margin-top: 5px; }
        .btn {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #6366f1, #3730a3);
            color: white; border: none; border-radius: 10px;
            font-size: 14.5px; font-weight: 600; font-family: 'DM Sans', 'Cairo', sans-serif;
            cursor: pointer; transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(79,70,229,0.35);
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(79,70,229,0.45); }
        .success-banner {
            background: #ecfdf5; border: 1px solid #a7f3d0;
            border-radius: 10px; padding: 14px 16px;
            font-size: 13.5px; color: #065f46; margin-bottom: 20px;
            display: flex; align-items: flex-start; gap: 10px;
        }
        .success-banner svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
        .back-link {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px; color: #4F46E5; text-decoration: none;
            font-weight: 500; margin-top: 20px; justify-content: center;
        }
        .back-link:hover { text-decoration: underline; }
        .back-link svg { width: 14px; height: 14px; }
        .auth-lang-switcher {
            position: fixed;
            top: 24px;
            inset-inline-end: 24px;
            z-index: 10;
        }
    </style>
</head>
<body>
    <div class="auth-lang-switcher">
        <x-language-switcher />
    </div>
    <div class="card">
        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>

        <h1>{{ __("Forgot your password?") }}</h1>
        <p class="sub">{{ __("Enter your email address and we'll send you a link to reset your password.") }}</p>

        @if(session('status'))
            <div class="success-banner">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">{{ __("Email Address") }}</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       placeholder="you@school.edu" required autocomplete="email">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn">{{ __("Send Reset Link") }}</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __("Back to login") }}
        </a>
    </div>
</body>
</html>
