<x-layouts.app pageTitle="Edit Subject">
    <style>
        .form-card {
            background: white; border-radius: 14px; border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04); padding: 28px; max-width: 560px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: #334155; margin-bottom: 6px;
        }
        .field-hint { font-size: 11.5px; color: #94a3b8; margin-top: 4px; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 24px; background: #4F46E5; color: white; border: none;
            border-radius: 10px; font-size: 14px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(79,70,229,0.3);
        }
        .btn-primary:hover { background: #4338ca; transform: translateY(-1px); }
        .btn-secondary {
            display: inline-flex; align-items: center; padding: 10px 24px;
            background: white; color: #64748b; border: 1px solid #e2e8f0;
            border-radius: 10px; font-size: 14px; font-weight: 600;
            font-family: 'DM Sans', sans-serif; cursor: pointer; text-decoration: none; transition: all 0.2s;
        }
        .btn-secondary:hover { border-color: #cbd5e1; color: #334155; }
        .error { color: #dc2626; font-size: 12px; margin-top: 4px; }
        .school-label {
            display: inline-block; padding: 8px 14px; background: #f8fafc;
            border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 14px; color: #64748b;
        }
    </style>

    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.subjects.index') }}" style="font-size: 13px; color: #64748b; text-decoration: none;">
            &larr; Back to Subjects
        </a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
            @csrf @method('PUT')

            <div class="form-group">
                <label>School</label>
                <div class="school-label">{{ $subject->school?->name ?? '—' }}</div>
                <div class="field-hint">School cannot be changed after creation.</div>
            </div>

            <div class="form-group">
                <label for="name">Subject Name</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $subject->name) }}" required>
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="code">Subject Code</label>
                <input type="text" id="code" name="code"
                       value="{{ old('code', $subject->code) }}" required>
                <div class="field-hint">Must be unique across all subjects.</div>
                @error('code')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('admin.subjects.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
