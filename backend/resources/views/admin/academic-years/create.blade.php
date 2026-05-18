<x-layouts.app pageTitle="Create Academic Year">
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
        .form-group input, .form-group select {
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
        .form-group input:focus, .form-group select:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
            background: white;
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
            accent-color: #4F46E5;
        }
        .checkbox-group label {
            margin-bottom: 0;
        }
        .btn-row {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }
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
        <a href="{{ route('admin.academic-years.index') }}" style="font-size: 13px; color: #64748b; text-decoration: none;">
            &larr; Back to Academic Years
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.academic-years.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Academic Year Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. 2025-2026" required>
                @error('name')
                    <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                    @error('start_date')
                        <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                    @error('end_date')
                        <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                    <label for="is_active">Set as active academic year</label>
                </div>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Create Academic Year
                </button>
                <a href="{{ route('admin.academic-years.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
