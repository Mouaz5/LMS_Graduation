<x-layouts.app pageTitle="User Management">
    <style>
        .page-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-desc { font-size: 13px; color: #64748b; }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 20px;
            background: #4F46E5;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(79,70,229,0.3);
        }
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(79,70,229,0.4);
        }
        .btn-primary svg { width: 15px; height: 15px; }
        .table-card {
            background: white;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            gap: 12px;
            flex-wrap: wrap;
        }
        .search-input {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            color: #374151;
            outline: none;
            background: #fafafa;
            width: 220px;
            transition: all 0.2s;
        }
        .search-input:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .table-meta { font-size: 12.5px; color: #94a3b8; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8fafc; }
        th {
            padding: 12px 16px;
            text-align: start;
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        th:first-child { padding-inline-start: 20px; }
        th:last-child { padding-inline-end: 20px; text-align: end; }
        .pagination-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid #f1f5f9;
            font-size: 13px;
            color: #64748b;
        }
    </style>

    <div class="page-actions">
        <div>
            <div style="font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a;">All Users</div>
            <div class="page-desc">{{ $users->total() }} users registered in the system</div>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create User
        </a>
    </div>

    <div class="table-card">
        <div class="table-toolbar">
            <input type="text" class="search-input" placeholder="Search users..." oninput="filterTable(this.value)">
            <div class="table-meta">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</div>
        </div>

        <div style="overflow-x: auto;">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <x-user-table-row :user="$user" />
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px; color: #cbd5e1; font-size: 14px;">
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="pagination-row">
            <div>Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</div>
            <div style="display: flex; gap: 6px; align-items: center;">
                @if($users->onFirstPage())
                    <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">← Prev</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; border: 1px solid #e2e8f0; color: #374151; text-decoration: none; font-size: 12px; font-weight: 600; transition: all 0.2s;">← Prev</a>
                @endif

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; background: #4F46E5; color: white; text-decoration: none; font-size: 12px; font-weight: 600; box-shadow: 0 2px 6px rgba(79,70,229,0.3);">Next →</a>
                @else
                    <span style="padding: 6px 12px; border-radius: 6px; background: #f8fafc; color: #cbd5e1; font-size: 12px; font-weight: 600;">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    <script>
    function filterTable(query) {
        const rows = document.querySelectorAll('#usersTable tbody tr');
        const q = query.toLowerCase();
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
    </script>
</x-layouts.app>
