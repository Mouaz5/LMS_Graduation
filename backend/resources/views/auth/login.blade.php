<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — SchoolLMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #4F46E5;
            --primary-dark: #3730a3;
            --primary-light: #818cf8;
            --accent: #10b981;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
            background: #0f0e1a;
        }

        /* Left panel — brand */
        .brand-panel {
            width: 44%;
            background: #1e1b4b;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px 56px;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 30% 30%, rgba(99,102,241,0.35) 0%, transparent 60%),
                radial-gradient(ellipse at 70% 70%, rgba(79,70,229,0.25) 0%, transparent 60%);
        }

        /* Geometric decorations */
        .geo-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(99,102,241,0.2);
        }

        .geo-ring-1 { width: 300px; height: 300px; top: -80px; right: -80px; }
        .geo-ring-2 { width: 200px; height: 200px; top: -40px; right: -40px; border-color: rgba(99,102,241,0.3); }
        .geo-ring-3 { width: 400px; height: 400px; bottom: -120px; left: -120px; }
        .geo-ring-4 { width: 250px; height: 250px; bottom: -80px; left: -80px; border-color: rgba(99,102,241,0.3); }

        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(99,102,241,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.06) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .brand-content {
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 48px;
        }

        .brand-logo-mark {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #818cf8, #4F46E5);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(79,70,229,0.5);
        }

        .brand-logo-mark svg { width: 26px; height: 26px; color: white; }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: white;
        }

        .brand-tagline {
            font-size: 11px;
            color: #a5b4fc;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .brand-headline {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 700;
            color: white;
            line-height: 1.25;
            margin-bottom: 16px;
        }

        .brand-headline em {
            font-style: italic;
            color: #818cf8;
        }

        .brand-desc {
            font-size: 14px;
            color: #c7d2fe;
            line-height: 1.7;
            max-width: 340px;
            margin-bottom: 48px;
        }

        .brand-features {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .brand-feature {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #a5b4fc;
        }

        .brand-feature-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--primary-light);
            flex-shrink: 0;
        }

        /* Right panel — form */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 40px 48px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        .form-header {
            margin-bottom: 36px;
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 14px;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
            letter-spacing: 0.3px;
        }

        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: #0f172a;
            background: #fafafa;
            transition: all 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
        }

        .form-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            inset-inline-start: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: #94a3b8;
            pointer-events: none;
        }

        .input-wrapper .form-input {
            padding-inline-start: 38px;
        }

        .error-msg {
            font-size: 12px;
            color: #ef4444;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--primary);
            border-radius: 4px;
        }

        .forgot-link {
            font-size: 13px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover { text-decoration: underline; }

        .submit-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #6366f1, var(--primary-dark));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14.5px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.2px;
            box-shadow: 0 4px 14px rgba(79,70,229,0.35);
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79,70,229,0.45);
        }

        .submit-btn:active { transform: translateY(0); }

        .form-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #cbd5e1;
            font-size: 12px;
        }

        .form-divider::before, .form-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .demo-credentials {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px;
            font-size: 12px;
            color: #64748b;
        }

        .demo-credentials strong {
            display: block;
            color: #374151;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .demo-cred-item {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }

        .demo-cred-item code {
            font-family: monospace;
            color: var(--primary);
            font-size: 12px;
        }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .brand-panel { width: 100%; padding: 32px 24px 24px; min-height: auto; }
            .brand-headline { font-size: 26px; }
            .brand-features { display: none; }
            .form-panel { padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <!-- Brand Panel -->
    <div class="brand-panel">
        <div class="geo-ring geo-ring-1"></div>
        <div class="geo-ring geo-ring-2"></div>
        <div class="geo-ring geo-ring-3"></div>
        <div class="geo-ring geo-ring-4"></div>
        <div class="grid-overlay"></div>

        <div class="brand-content">
            <div class="brand-logo">
                <div class="brand-logo-mark">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21a12.083 12.083 0 01-6.16-10.422L12 14z"/>
                    </svg>
                </div>
                <div>
                    <div class="brand-name">SchoolLMS</div>
                    <div class="brand-tagline">Management System</div>
                </div>
            </div>

            <h1 class="brand-headline">
                Education<br>
                <em>Reimagined</em><br>
                for the Modern Era
            </h1>

            <p class="brand-desc">
                A comprehensive school management platform designed for administrators, teachers, students, and parents.
            </p>

            <div class="brand-features">
                <div class="brand-feature">
                    <div class="brand-feature-dot"></div>
                    Role-based access for all stakeholders
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-dot"></div>
                    Real-time grades and attendance tracking
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-dot"></div>
                    Arabic RTL support built-in
                </div>
                <div class="brand-feature">
                    <div class="brand-feature-dot"></div>
                    Mobile-first responsive design
                </div>
            </div>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="form-panel">
        <div class="login-card">
            <div class="form-header">
                <h2 class="form-title">Welcome back</h2>
                <p class="form-subtitle">Sign in to your school account</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST" novalidate>
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@school.edu"
                            class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                            autocomplete="email"
                            required
                        >
                    </div>
                    @error('email')
                        <div class="error-msg">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                            autocomplete="current-password"
                            required
                        >
                    </div>
                    @error('password')
                        <div class="error-msg">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-footer">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="submit-btn">
                    Sign In to Dashboard
                </button>
            </form>

        </div>
    </div>
</body>
</html>
