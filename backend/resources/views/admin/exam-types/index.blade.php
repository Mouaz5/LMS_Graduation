<x-layouts.app pageTitle="Exam Types">
<style>
    .page-header { margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .page-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #0f172a; }

    .card {
        background: white;
        border-radius: 14px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-title { font-size: 14px; font-weight: 700; color: #0f172a; }

    .form-row { display: flex; gap: 12px; flex-wrap: wrap; padding: 20px; align-items: flex-end; }
    .form-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 160px; }
    .form-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.7px; }
    .form-input, .form-select {
        padding: 9px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13.5px;
        font-family: 'DM Sans', sans-serif;
        color: #374151;
        background: #fafafa;
        outline: none;
        transition: border 0.2s;
    }
    .form-input:focus, .form-select:focus { border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }

    .btn-primary {
        padding: 9px 20px;
        background: #4F46E5;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        transition: background 0.2s;
        white-space: nowrap;
    }
    .btn-primary:hover { background: #3730a3; }
    .btn-danger {
        padding: 6px 12px;
        background: #fee2e2;
        color: #991b1b;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
    }
    .btn-danger:hover { background: #fecaca; }
    .btn-edit {
        padding: 6px 12px;
        background: #dbeafe;
        color: #1e40af;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
    }
    .btn-edit:hover { background: #bfdbfe; }

    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #f8fafc; }
    th { padding: 12px 16px; text-align: start; font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: 0.8px; text-transform: uppercase; }
    td { padding: 12px 16px; border-bottom: 1px solid #f8fafc; font-size: 13.5px; color: #374151; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafafa; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
    .badge-purple { background: #ede9fe; color: #5b21b6; }

    .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 500; }
    .alert-success { background: #dcfce7; color: #166534; }
    .alert-error { background: #fee2e2; color: #991b1b; }

    .modal-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 100;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: white;
        border-radius: 16px;
        padding: 28px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .modal-title { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 20px; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
    .btn-secondary { padding: 9px 20px; background: #f1f5f9; color: #374151; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif; }
    .btn-secondary:hover { background: #e2e8f0; }
</style>

<div class="page-header">
    <div>
        <div class="page-title">Exam Types</div>
        <div class="page-desc">Define exam types and their grade weights per semester</div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

{{-- Create form --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Add Exam Type</span>
    </div>
    <form method="POST" action="{{ route('admin.exam-types.store') }}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input class="form-input" name="name" placeholder="e.g. Midterm" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Weight %</label>
                <input class="form-input" name="weight_percent" type="number" step="0.01" min="0.01" max="100"
                    placeholder="e.g. 30" value="{{ old('weight_percent') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Semester</label>
                <select class="form-select" name="semester_id" required>
                    <option value="">Select semester…</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ old('semester_id') == $sem->id ? 'selected' : '' }}>
                            {{ $sem->academicYear->name ?? '—' }} — {{ $sem->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn-primary">Add</button>
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">All Exam Types</span>
        <span style="font-size:12px; color:#94a3b8;">{{ $examTypes->total() }} total</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Weight</th>
                <th>Semester</th>
                <th>Academic Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($examTypes as $et)
                <tr>
                    <td style="font-weight:600">{{ $et->name }}</td>
                    <td><span class="badge badge-purple">{{ $et->weight_percent }}%</span></td>
                    <td>{{ $et->semester->name ?? '—' }}</td>
                    <td>{{ $et->semester->academicYear->name ?? '—' }}</td>
                    <td style="display:flex; gap:8px;">
                        <button class="btn-edit" onclick="openEdit({{ $et->id }}, '{{ addslashes($et->name) }}', {{ $et->weight_percent }})">Edit</button>
                        <form method="POST" action="{{ route('admin.exam-types.destroy', $et) }}"
                              onsubmit="return confirm('Delete this exam type?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#94a3b8; padding:32px;">No exam types yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if($examTypes->hasPages())
        <div style="padding:16px 20px;">{{ $examTypes->links() }}</div>
    @endif
</div>

{{-- Edit Modal --}}
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-title">Edit Exam Type</div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Name</label>
                <input class="form-input" name="name" id="editName" required style="width:100%">
            </div>
            <div class="form-group">
                <label class="form-label">Weight %</label>
                <input class="form-input" name="weight_percent" id="editWeight" type="number" step="0.01" min="0.01" max="100" required style="width:100%">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeEdit()">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, name, weight) {
    document.getElementById('editName').value = name;
    document.getElementById('editWeight').value = weight;
    document.getElementById('editForm').action = `/admin/exam-types/${id}`;
    document.getElementById('editModal').classList.add('open');
}
function closeEdit() {
    document.getElementById('editModal').classList.remove('open');
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEdit();
});
</script>
</x-layouts.app>
