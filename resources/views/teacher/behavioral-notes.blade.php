<x-layouts.app :pageTitle="__('Behavioral Notes')">
    <style>
        .page-header { margin-bottom: 20px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; }

        .two-col { display: grid; grid-template-columns: 380px 1fr; gap: 20px; align-items: start; }
        @media(max-width: 900px) { .two-col { grid-template-columns: 1fr; } }

        .card {
            background: white; border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .card-header {
            padding: 18px 20px; border-bottom: 1px solid #f1f5f9;
            font-size: 14px; font-weight: 700; color: #0f172a;
        }
        .card-body { padding: 20px; }

        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px; }
        .form-control {
            width: 100%; padding: 9px 14px;
            border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13.5px; font-family: 'DM Sans', sans-serif; color: #374151;
            background: #fafafa; outline: none; transition: border 0.2s;
            box-sizing: border-box;
        }
        .form-control:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        textarea.form-control { resize: vertical; min-height: 90px; }

        .severity-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .severity-option { display: none; }
        .severity-label {
            display: flex; flex-direction: column; align-items: center; gap: 4px;
            padding: 10px 8px; border-radius: 10px; border: 2px solid #e2e8f0;
            cursor: pointer; transition: all 0.15s; text-align: center;
        }
        .severity-label .sev-icon { font-size: 20px; }
        .severity-label .sev-text { font-size: 11.5px; font-weight: 700; }
        .severity-option:checked + .severity-label.info     { border-color: #6366f1; background: #eef2ff; color: #3730a3; }
        .severity-option:checked + .severity-label.warning  { border-color: #f59e0b; background: #fef3c7; color: #78350f; }
        .severity-option:checked + .severity-label.critical { border-color: #ef4444; background: #fee2e2; color: #991b1b; }

        .btn-submit {
            width: 100%; padding: 11px;
            background: #4F46E5; color: white; border: none; border-radius: 10px;
            font-size: 13.5px; font-weight: 600; font-family: 'DM Sans', sans-serif;
            cursor: pointer; transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(79,70,229,0.3);
        }
        .btn-submit:hover { background: #4338ca; transform: translateY(-1px); }

        /* Notes list */
        .notes-list { display: flex; flex-direction: column; gap: 0; }
        .note-item {
            padding: 16px 20px; border-bottom: 1px solid #f8fafc;
            display: flex; gap: 14px; align-items: flex-start;
        }
        .note-item:last-child { border-bottom: none; }
        .note-severity-bar {
            width: 4px; border-radius: 4px; flex-shrink: 0; align-self: stretch;
            min-height: 50px;
        }
        .sev-info     { background: #6366f1; }
        .sev-warning  { background: #f59e0b; }
        .sev-critical { background: #ef4444; }

        .note-content { flex: 1; }
        .note-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; flex-wrap: wrap; gap: 6px; }
        .note-student { font-weight: 700; color: #0f172a; font-size: 13.5px; }
        .note-meta { font-size: 12px; color: #94a3b8; }
        .note-text { font-size: 13px; color: #475569; line-height: 1.5; }

        .badge {
            display: inline-flex; align-items: center;
            padding: 2px 9px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .badge-info     { background: #eef2ff; color: #3730a3; }
        .badge-warning  { background: #fef3c7; color: #78350f; }
        .badge-critical { background: #fee2e2; color: #991b1b; }

        .empty-state { text-align: center; padding: 48px 20px; }

        .error-msg { font-size: 12px; color: #ef4444; margin-top: 4px; }
    </style>

    <div class="page-header">
        <div class="page-title">{{ __("Behavioral Notes") }}</div>
        <div class="page-desc">{{ __("Create notes for students and view your previously submitted notes") }}</div>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 12px 16px; border-radius: 10px; font-size: 13.5px; font-weight: 500; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="two-col">
        {{-- Create Form --}}
        <div class="card">
            <div class="card-header">{{ __("Add New Note") }}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('teacher.behavioral-notes.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">{{ __("Student") }}</label>
                        <select name="student_user_id" class="form-control" required>
                            <option value="">{{ __("— Select Student —") }}</option>
                            @foreach($students->groupBy(fn($s) => $s->classroom->name ?? __('Unknown')) as $classroomName => $classStudents)
                                <optgroup label="{{ $classroomName }}">
                                    @foreach($classStudents as $profile)
                                        <option value="{{ $profile->user_id }}" @selected(old('student_user_id') == $profile->user_id)>
                                            {{ $profile->student->name ?? '—' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('student_user_id')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __("Date") }}</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}" required>
                        @error('date')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __("Severity") }}</label>
                        <div class="severity-grid">
                            @foreach(['info' => ['📘', __('Info')], 'warning' => ['⚠️', __('Warning')], 'critical' => ['🚨', __('Critical')]] as $val => [$icon, $label])
                                <div>
                                    <input type="radio" name="severity" id="sev_{{ $val }}" value="{{ $val }}" class="severity-option"
                                           @checked(old('severity', 'info') === $val)>
                                    <label for="sev_{{ $val }}" class="severity-label {{ $val }}">
                                        <span class="sev-icon">{{ $icon }}</span>
                                        <span class="sev-text">{{ $label }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('severity')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">{{ __("Note") }}</label>
                        <textarea name="note" class="form-control" placeholder="{{ __('Describe the behavior...') }}" required>{{ old('note') }}</textarea>
                        @error('note')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn-submit">{{ __("Add Note") }}</button>
                </form>
            </div>
        </div>

        {{-- Notes List --}}
        <div class="card">
            <div class="table-header">
                <div class="table-title">{{ __("My Notes") }}</div>
                <div class="table-meta">{{ __(":count total", ['count' => $notes->total()]) }}</div>
            </div>

            @if($notes->isEmpty())
                <div class="empty-state">
                    <div class="empty-title">{{ __("No Notes Yet") }}</div>
                    <div class="empty-desc">{{ __("Submit a note using the form on the left.") }}</div>
                </div>
            @else
                <div class="notes-list">
                    @foreach($notes as $note)
                        <div class="note-item">
                            <div class="note-severity-bar sev-{{ $note->severity }}"></div>
                            <div class="note-content">
                                <div class="note-header">
                                    <div class="note-student">{{ $note->student->name ?? '—' }}</div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span class="badge badge-{{ $note->severity }}">{{ ucfirst($note->severity) }}</span>
                                        <span class="note-meta">{{ $note->date?->format('M j, Y') }}</span>
                                    </div>
                                </div>
                                <div class="note-text">{{ $note->note }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($notes->hasPages())
                    <div class="pagination-row">
                        <div>{{ __("Page :current of :last", ['current' => $notes->currentPage(), 'last' => $notes->lastPage()]) }}</div>
                        <div style="display: flex; gap: 6px;">
                            @if($notes->onFirstPage())
                                <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">{{ __("← Prev") }}</span>
                            @else
                                <a href="{{ $notes->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; border: 1px solid #e2e8f0; color: #374151; text-decoration: none; font-size: 12px; font-weight: 600;">{{ __("← Prev") }}</a>
                            @endif
                            @if($notes->hasMorePages())
                                <a href="{{ $notes->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #4F46E5; color: white; text-decoration: none; font-size: 12px; font-weight: 600;">{{ __("Next →") }}</a>
                            @else
                                <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">{{ __("Next →") }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
