<x-layouts.app :pageTitle="__('Create User')">
    <style>
        .form-container {
            max-width: 600px;
        }
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .form-header {
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }
        .form-header-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .form-header-sub { font-size: 13px; color: #64748b; }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }
        .form-label {
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            letter-spacing: 0.2px;
        }
        .form-label .req { color: #ef4444; margin-inline-start: 2px; }
        .form-input, .form-select {
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: #0f172a;
            background: #fafafa;
            outline: none;
            transition: all 0.2s;
            width: 100%;
        }
        .form-input:focus, .form-select:focus {
            border-color: #4F46E5;
            background: white;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
        }
        .form-input.is-error { border-color: #ef4444; }
        .field-error {
            font-size: 11.5px;
            color: #ef4444;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .role-options {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        @media (max-width: 640px) { .role-options { grid-template-columns: repeat(2, 1fr); } }
        .role-option { position: relative; }
        .role-option input { position: absolute; opacity: 0; }
        .role-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 12px 8px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-align: center;
        }
        .role-option input:checked + label {
            border-color: #4F46E5;
            background: #eef2ff;
            color: #4338ca;
        }
        .role-option label:hover { border-color: #c7d2fe; background: #f5f7ff; }
        .role-icon { font-size: 20px; }
        .form-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }
        .btn-submit {
            padding: 11px 28px;
            background: #4F46E5;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(79,70,229,0.3);
        }
        .btn-submit:hover { background: #4338ca; transform: translateY(-1px); }
        .btn-cancel {
            padding: 11px 20px;
            background: transparent;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-cancel:hover { background: #f8fafc; color: #374151; }
    </style>

    <div class="form-container">
        {{-- Breadcrumb --}}
        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #94a3b8; margin-bottom: 16px;">
            <a href="{{ route('admin.users.index') }}" style="color: #4F46E5; text-decoration: none; font-weight: 500;">{{ __("Users") }}</a>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>{{ __("Create New User") }}</span>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-header-title">{{ __("Create New User") }}</div>
                <div class="form-header-sub">{{ __("Add a new account to the school system") }}</div>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" novalidate>
                @csrf

                <div class="form-grid">
                    {{-- Name --}}
                    <div class="form-group">
                        <label class="form-label" for="name">{{ __("Full Name") }} <span class="req">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               placeholder="e.g. Ahmad Al-Rashid"
                               class="form-input {{ $errors->has('name') ? 'is-error' : '' }}">
                        @error('name')
                            <div class="field-error">
                                <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="form-group">
                        <label class="form-label" for="phone">{{ __("Phone Number") }}</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                               placeholder="+962 79 xxx xxxx"
                               class="form-input {{ $errors->has('phone') ? 'is-error' : '' }}">
                        @error('phone')
                            <div class="field-error">
                                <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group full">
                        <label class="form-label" for="email">{{ __("Email Address") }} <span class="req">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               placeholder="user@school.edu"
                               class="form-input {{ $errors->has('email') ? 'is-error' : '' }}">
                        @error('email')
                            <div class="field-error">
                                <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label class="form-label" for="password">{{ __("Password") }} <span class="req">*</span></label>
                        <input type="password" id="password" name="password"
                               placeholder="{{ __('Min. 8 characters') }}"
                               class="form-input {{ $errors->has('password') ? 'is-error' : '' }}">
                        @error('password')
                            <div class="field-error">
                                <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">{{ __("Confirm Password") }} <span class="req">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="{{ __('Repeat password') }}"
                               class="form-input">
                    </div>

                    {{-- Role --}}
                    <div class="form-group full">
                        <label class="form-label">{{ __("Role") }} <span class="req">*</span></label>
                        <div class="role-options">
                            @foreach([
                                ['admin',   __('Admin'),   '🔑'],
                                ['teacher', __('Teacher'), '📚'],
                                ['student', __('Student'), '🎓'],
                                ['parent',  __('Parent'),  '👨‍👩‍👧'],
                            ] as [$value, $label, $emoji])
                            <div class="role-option">
                                <input type="radio" name="role" id="role_{{ $value }}" value="{{ $value }}"
                                       {{ old('role', 'student') === $value ? 'checked' : '' }}>
                                <label for="role_{{ $value }}">
                                    <span class="role-icon">{{ $emoji }}</span>
                                    {{ $label }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('role')
                            <div class="field-error">
                                <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">{{ __("Create User") }}</button>
                    <a href="{{ route('admin.users.index') }}" class="btn-cancel">{{ __("Cancel") }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
