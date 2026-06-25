<x-layouts.app :pageTitle="__('Behavioral Notes')">
    <style>
        .page-header { margin-bottom: 20px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; }

        .child-selector {
            background: white; border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            padding: 16px 20px; margin-bottom: 20px;
            display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;
        }
        .filter-select {
            padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13px; font-family: 'DM Sans', sans-serif; color: #374151;
            background: #fafafa; outline: none; transition: border 0.2s; min-width: 200px;
        }

        .card {
            background: white; border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .card-header {
            padding: 18px 20px; border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 14px; font-weight: 700; color: #0f172a; }
        .card-meta { font-size: 12.5px; color: #94a3b8; }

        .notes-list { display: flex; flex-direction: column; }
        .note-item {
            padding: 18px 20px; border-bottom: 1px solid #f8fafc;
            display: flex; gap: 14px; align-items: flex-start;
        }
        .note-item:last-child { border-bottom: none; }

        .note-severity-bar {
            width: 4px; border-radius: 4px; flex-shrink: 0; align-self: stretch; min-height: 56px;
        }
        .sev-info     { background: #6366f1; }
        .sev-warning  { background: #f59e0b; }
        .sev-critical { background: #ef4444; }

        .note-content { flex: 1; }
        .note-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 8px; gap: 8px; flex-wrap: wrap; }
        .note-teacher { font-weight: 700; color: #0f172a; font-size: 13.5px; }
        .note-meta { font-size: 12px; color: #94a3b8; }
        .note-text { font-size: 13px; color: #475569; line-height: 1.5; }

        .badge {
            display: inline-flex; align-items: center;
            padding: 2px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .badge-info     { background: #eef2ff; color: #3730a3; }
        .badge-warning  { background: #fef3c7; color: #78350f; }
        .badge-critical { background: #fee2e2; color: #991b1b; }

        .empty-state { text-align: center; padding: 56px 20px; }
        .empty-icon {
            width: 56px; height: 56px; background: #f1f5f9; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
        }
    </style>

    <div class="page-header">
        <div class="page-title">{{ __("Behavioral Notes") }}</div>
        <div class="page-desc">{{ __("View behavioral notes written by teachers for your child") }}</div>
    </div>

    {{-- Child selector --}}
    @if($children->count() > 1)
        <form method="GET" action="{{ route('parent.behavioral-notes') }}">
            <div class="child-selector">
                <div class="filter-group">
                    <label class="filter-label">{{ __("Child") }}</label>
                    <select name="child_id" class="filter-select" onchange="this.form.submit()">
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" @selected($selectedChild?->id == $child->id)>
                                {{ $child->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    @endif

    @if(!$selectedChild)
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                </div>
                <div class="empty-title">{{ __("No Children Linked") }}</div>
                <div class="empty-desc">{{ __("No children are linked to your account yet.") }}</div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{ __(":name's Notes", ['name' => $selectedChild->name]) }}</div>
                <div class="card-meta">{{ __(":count notes", ['count' => $notes->total()]) }}</div>
            </div>

            @if($notes->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="24" height="24" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="empty-title">{{ __("No Notes") }}</div>
                    <div class="empty-desc">{{ __("No behavioral notes have been written for :name yet.", ['name' => $selectedChild->name]) }}</div>
                </div>
            @else
                <div class="notes-list">
                    @foreach($notes as $note)
                        <div class="note-item">
                            <div class="note-severity-bar sev-{{ $note->severity }}"></div>
                            <div class="note-content">
                                <div class="note-header">
                                    <div>
                                        <div class="note-teacher">{{ $note->teacher->name ?? '—' }}</div>
                                        <div class="note-meta">{{ $note->date?->format('l, M j, Y') }}</div>
                                    </div>
                                    <span class="badge badge-{{ $note->severity }}">{{ ucfirst($note->severity) }}</span>
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
                                <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">← {{ __("Prev") }}</span>
                            @else
                                <a href="{{ $notes->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; border: 1px solid #e2e8f0; color: #374151; text-decoration: none; font-size: 12px; font-weight: 600;">← {{ __("Prev") }}</a>
                            @endif
                            @if($notes->hasMorePages())
                                <a href="{{ $notes->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #4F46E5; color: white; text-decoration: none; font-size: 12px; font-weight: 600;">{{ __("Next") }} →</a>
                            @else
                                <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">{{ __("Next") }} →</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif
</x-layouts.app>
