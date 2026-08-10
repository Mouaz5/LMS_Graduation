<x-layouts.app :pageTitle="__('Create Subject')">
    <style>
        .form-card {
            background: white; border-radius: 14px; border: 1px solid var(--border-soft);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 28px; max-width: 560px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--text-primary); margin-bottom: 6px;
        }
        .field-hint { font-size: 11.5px; color: var(--text-muted); margin-top: 4px; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 24px; background: var(--primary); color: white; border: none;
            border-radius: 10px; font-size: 14px; font-weight: 600;
            font-family: var(--font-body); cursor: pointer; transition: all 0.2s;
            box-shadow: 0 2px 8px color-mix(in srgb, var(--primary) 30%, transparent);
        }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-secondary {
            display: inline-flex; align-items: center; padding: 10px 24px;
            background: white; color: var(--text-secondary); border: 1px solid var(--border);
            border-radius: 10px; font-size: 14px; font-weight: 600;
            font-family: var(--font-body); cursor: pointer; text-decoration: none; transition: all 0.2s;
        }
        .btn-secondary:hover { border-color: var(--text-faint); color: var(--text-primary); }
        .error { color: var(--danger-dark); font-size: 12px; margin-top: 4px; }
    </style>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.subjects.index') }}" style="font-size: 13px; color: var(--text-secondary); text-decoration: none;">
            &larr; {{ __("Back to Subjects") }}
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.subjects.store') }}">
            @csrf

            <div class="form-group">
                <label for="school_id">{{ __("School") }}</label>
                <select id="school_id" name="school_id" required>
                    <option value="">{{ __("Select school…") }}</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
                @error('school_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="name">{{ __("Subject Name") }}</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       placeholder="{{ __('e.g. Mathematics') }}" required>
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="code">{{ __("Subject Code") }}</label>
                <input type="text" id="code" name="code" value="{{ old('code') }}"
                       placeholder="{{ __('e.g. MATH101') }}" required>
                <div class="field-hint">{{ __("Must be unique across all subjects.") }}</div>
                @error('code')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __("Create Subject") }}
                </button>
                <a href="{{ route('admin.subjects.index') }}" class="btn-secondary">{{ __("Cancel") }}</a>
            </div>
        </form>
    </div>
</x-layouts.app>
