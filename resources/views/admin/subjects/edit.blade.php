<x-layouts.app :pageTitle="__('Edit Subject')">
    <style>
        .form-card {
            background: var(--surface); border-radius: 14px; border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-card); padding: 28px; max-width: 560px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--text-primary); margin-bottom: 6px;
        }
        .field-hint { font-size: 11.5px; color: var(--text-muted); margin-top: 4px; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 24px; background: var(--primary); color: var(--on-primary); border: none;
            border-radius: 10px; font-size: 14px; font-weight: 600;
            font-family: var(--font-body); cursor: pointer; transition: all 0.2s;
            box-shadow: 0 2px 8px color-mix(in srgb, var(--primary) 30%, transparent);
        }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-secondary {
            display: inline-flex; align-items: center; padding: 10px 24px;
            background: var(--surface); color: var(--text-secondary); border: 1px solid var(--border);
            border-radius: 10px; font-size: 14px; font-weight: 600;
            font-family: var(--font-body); cursor: pointer; text-decoration: none; transition: all 0.2s;
        }
        .btn-secondary:hover { border-color: var(--text-faint); color: var(--text-primary); }
        .error { color: var(--danger-dark); font-size: 12px; margin-top: 4px; }
        .school-label {
            display: inline-block; padding: 8px 14px; background: var(--surface-2);
            border: 1.5px solid var(--border); border-radius: 10px; font-size: 14px; color: var(--text-secondary);
        }
    </style>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.subjects.index') }}" style="font-size: 13px; color: var(--text-secondary); text-decoration: none;">
            &larr; Back to Subjects
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label>{{ __("School") }}</label>
                <div class="school-label">{{ $subject->school?->name ?? '—' }}</div>
                <div class="field-hint">{{ __("School cannot be changed after creation.") }}</div>
            </div>

            <div class="form-group">
                <label for="name">{{ __("Subject Name") }}</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $subject->name) }}" required>
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="code">{{ __("Subject Code") }}</label>
                <input type="text" id="code" name="code"
                       value="{{ old('code', $subject->code) }}" required>
                <div class="field-hint">{{ __("Must be unique across all subjects.") }}</div>
                @error('code')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary">{{ __("Save Changes") }}</button>
                <a href="{{ route('admin.subjects.index') }}" class="btn-secondary">{{ __("Cancel") }}</a>
            </div>
        </form>
    </div>
</x-layouts.app>
