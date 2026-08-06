<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Reset Password') }} — SchoolLMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:wght@600;700&family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', 'Cairo', sans-serif;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: #f8fafc; -webkit-font-smoothing: antialiased;
        }
        .card {
            background: white; border-radius: 20px; padding: 40px;
            width: 100%; max-width: 420px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07); border: 1px solid #e2e8f0;
        }
        h1 { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
        [dir="rtl"] body, [dir="rtl"] h1 { font-family: 'Cairo', sans-serif; }
        .sub { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 7px; }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 14px; font-family: 'DM Sans', 'Cairo', sans-serif; color: #0f172a;
            background: #fafafa; outline: none; transition: all 0.2s;
        }
        input:focus { border-color: #4F46E5; background: white; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
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
        .back-link {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px; color: #4F46E5; text-decoration: none;
            font-weight: 500; margin-top: 20px; justify-content: center;
        }
        .back-link:hover { text-decoration: underline; }
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
        <h1>{{ __("Reset your password") }}</h1>
        <p class="sub">{{ __("Choose a new password for your account.") }}</p>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">{{ __("Email Address") }}</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}"
                       required autocomplete="email">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">{{ __("New Password") }}</label>
                <input type="password" id="password" name="password"
                       required autocomplete="new-password">
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ __("Confirm New Password") }}</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       required autocomplete="new-password">
            </div>

            <button type="submit" class="btn">{{ __("Reset Password") }}</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">{{ __("Back to login") }}</a>
    </div>
</body>
</html>
