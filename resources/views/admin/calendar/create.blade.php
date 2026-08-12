<x-layouts.app :pageTitle="__('Add Calendar Event')">
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
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: var(--font-body);
            color: var(--text-primary);
            background: var(--surface-3);
            transition: all 0.2s;
            outline: none;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 10%, transparent);
            background: var(--surface);
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
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
        <a href="{{ route('admin.calendar.index') }}" style="font-size: 13px; color: var(--text-secondary); text-decoration: none;">
            &larr; {{ __("Back to Calendar") }}
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.calendar.store') }}">
            @csrf

            <div class="form-group">
                <label for="date">{{ __("Date") }}</label>
                <input type="date" id="date" name="date" value="{{ old('date') }}" required>
                @error('date')
                    <div style="color: var(--danger-dark); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="type">{{ __("Type") }}</label>
                <select id="type" name="type" required>
                    <option value="">{{ __("Select type...") }}</option>
                    <option value="holiday" {{ old('type') === 'holiday' ? 'selected' : '' }}>{{ __("Holiday") }}</option>
                    <option value="event" {{ old('type') === 'event' ? 'selected' : '' }}>{{ __("Event") }}</option>
                    <option value="exam" {{ old('type') === 'exam' ? 'selected' : '' }}>{{ __("Exam") }}</option>
                </select>
                @error('type')
                    <div style="color: var(--danger-dark); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">{{ __("Description") }}</label>
                <textarea id="description" name="description" placeholder="{{ __('e.g. Spring Break, Midterm Exams') }}" required>{{ old('description') }}</textarea>
                @error('description')
                    <div style="color: var(--danger-dark); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __("Add Event") }}
                </button>
                <a href="{{ route('admin.calendar.index') }}" class="btn-secondary">{{ __("Cancel") }}</a>
            </div>
        </form>
    </div>
</x-layouts.app>
