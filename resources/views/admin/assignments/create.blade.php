<x-layouts.app :pageTitle="__('Create Assignment')">
    <style>
        .page-header { margin-bottom: 28px; }
        .page-header h2 {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        .page-header p { font-size: 13px; color: var(--text-secondary); }
        .form-card {
            background: var(--surface);
            border-radius: 14px;
            border: 1px solid var(--border-soft);
            box-shadow: var(--shadow-card);
            padding: 28px;
            max-width: 600px;
        }
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-strong);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: var(--font-body);
            color: var(--text-primary);
            background: var(--surface-3);
            transition: all 0.2s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%2394a3b8' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 18px;
            cursor: pointer;
        }
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 10%, transparent);
            background-color: var(--surface);
        }
        .form-select option { padding: 4px 8px; }
        .form-select optgroup { font-weight: 700; color: var(--text-strong); }
        .form-select option[value=""] { color: var(--text-muted); }
        .form-help {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border-soft);
        }
        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            background: var(--primary);
            color: var(--on-primary);
            border-radius: 10px;
            border: none;
            font-size: 13.5px;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 8px color-mix(in srgb, var(--primary) 30%, transparent);
        }
        .btn-save:hover { background: var(--primary-dark); box-shadow: 0 4px 14px color-mix(in srgb, var(--primary) 40%, transparent); transform: translateY(-1px); }
        .btn-save svg { width: 15px; height: 15px; }
        .btn-cancel {
            display: inline-flex;
            align-items: center;
            padding: 10px 22px;
            background: transparent;
            color: var(--text-secondary);
            border-radius: 10px;
            border: 1.5px solid var(--border);
            font-size: 13.5px;
            font-weight: 600;
            font-family: var(--font-body);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-cancel:hover { border-color: var(--text-faint); color: var(--text-strong); }
    </style>

    <div class="page-header">
        <h2>{{ __("Create Teacher Assignment") }}</h2>
        <p>{{ __("Assign a teacher to a subject in a specific classroom and academic year") }}</p>
    </div>

    @if($errors->any())
    <div style="background: var(--danger-tint); border: 1px solid var(--danger-border); border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;">
        <div style="color: var(--danger-text); font-size: 13px; font-weight: 600; margin-bottom: 4px;">{{ __("Please fix the following errors:") }}</div>
        <ul style="margin: 0; padding-inline-start: 18px; color: var(--danger-dark); font-size: 12.5px;">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.assignments.store') }}">
        @csrf

        <div class="form-card">
            <div class="form-group">
                <label class="form-label" for="teacher_user_id">{{ __("Teacher") }}</label>
                <select class="form-select @error('teacher_user_id') border-red-400 @enderror" id="teacher_user_id" name="teacher_user_id" required>
                    <option value="">{{ __("-- Select Teacher --") }}</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}"
                                data-subject-ids="{{ $teacher->teacherAssignments->pluck('subject_id')->implode(',') }}"
                                @selected(old('teacher_user_id') == $teacher->id)>{{ $teacher->name }}</option>
                    @endforeach
                </select>
                @error('teacher_user_id')
                    <div style="font-size: 12px; color: var(--danger-dark); margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="subject_id">{{ __("Subject") }}</label>
                <select class="form-select @error('subject_id') border-red-400 @enderror" id="subject_id" name="subject_id" required>
                    <option value="">{{ __("-- Select Subject --") }}</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }} ({{ $subject->code }})</option>
                    @endforeach
                </select>
                @error('subject_id')
                    <div style="font-size: 12px; color: var(--danger-dark); margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="classroom_id">{{ __("Classroom") }}</label>
                <select class="form-select @error('classroom_id') border-red-400 @enderror" id="classroom_id" name="classroom_id" required>
                    <option value="">{{ __("-- Select Classroom --") }}</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" @selected(old('classroom_id') == $classroom->id)>{{ $classroom->grade->name }} — {{ $classroom->name }}</option>
                    @endforeach
                </select>
                @error('classroom_id')
                    <div style="font-size: 12px; color: var(--danger-dark); margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="academic_year_id">{{ __("Academic Year") }}</label>
                <select class="form-select @error('academic_year_id') border-red-400 @enderror" id="academic_year_id" name="academic_year_id" required>
                    <option value="">{{ __("-- Select Academic Year --") }}</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id)>{{ $year->name }}</option>
                    @endforeach
                </select>
                <div class="form-help">{{ __("Only one assignment per teacher-subject-classroom-year combination is allowed.") }}</div>
                @error('academic_year_id')
                    <div style="font-size: 12px; color: var(--danger-dark); margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __("Create Assignment") }}
                </button>
                <a href="{{ route('admin.assignments.index') }}" class="btn-cancel">{{ __("Cancel") }}</a>
            </div>
        </div>
    </form>

</x-layouts.app>
