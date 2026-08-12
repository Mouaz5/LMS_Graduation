<x-layouts.app :pageTitle="__('Create Academic Year')">
    <style>
        .form-card {
            background: var(--surface);
            border-radius: 14px;
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-card);
            padding: 28px;
            max-width: 560px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }
        .checkbox-group label {
            margin-bottom: 0;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 24px;
            background: var(--primary);
            color: var(--on-primary);
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 8px color-mix(in srgb, var(--primary) 30%, transparent);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px color-mix(in srgb, var(--primary) 40%, transparent);
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            padding: 10px 24px;
            background: var(--surface);
            color: var(--text-secondary);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            border-color: var(--text-faint);
            color: var(--text-primary);
        }
    </style>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.academic-years.index') }}" style="font-size: 13px; color: var(--text-secondary); text-decoration: none;">
            &larr; {{ __("Back to Academic Years") }}
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.academic-years.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">{{ __("Academic Year Name") }}</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('e.g. 2025-2026') }}" required>
                @error('name')
                    <div style="color: var(--danger-dark); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_date">{{ __("Start Date") }}</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                    @error('start_date')
                        <div style="color: var(--danger-dark); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_date">{{ __("End Date") }}</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                    @error('end_date')
                        <div style="color: var(--danger-dark); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                    <label for="is_active">{{ __("Set as active academic year") }}</label>
                </div>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __("Create Academic Year") }}
                </button>
                <a href="{{ route('admin.academic-years.index') }}" class="btn-secondary">{{ __("Cancel") }}</a>
            </div>
        </form>
    </div>
</x-layouts.app>
