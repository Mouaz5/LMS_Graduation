<x-layouts.app pageTitle="Add Calendar Event">
    <style>
        .form-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
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
            color: #334155;
            margin-bottom: 6px;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: #0f172a;
            background: #fafafa;
            transition: all 0.2s;
            outline: none;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
            background: white;
        }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 24px;
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
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(79,70,229,0.4);
        }
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            padding: 10px 24px;
            background: white;
            color: #64748b;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            border-color: #cbd5e1;
            color: #334155;
        }
    </style>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.calendar.index') }}" style="font-size: 13px; color: #64748b; text-decoration: none;">
            &larr; Back to Calendar
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.calendar.store') }}">
            @csrf

            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" value="{{ old('date') }}" required>
                @error('date')
                    <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" required>
                    <option value="">Select type...</option>
                    <option value="holiday" {{ old('type') === 'holiday' ? 'selected' : '' }}>Holiday</option>
                    <option value="event" {{ old('type') === 'event' ? 'selected' : '' }}>Event</option>
                    <option value="exam" {{ old('type') === 'exam' ? 'selected' : '' }}>Exam</option>
                </select>
                @error('type')
                    <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="e.g. Spring Break, Midterm Exams" required>{{ old('description') }}</textarea>
                @error('description')
                    <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Event
                </button>
                <a href="{{ route('admin.calendar.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
