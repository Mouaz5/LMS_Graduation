<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['studentProfile.classroom.grade', 'parents', 'children', 'teacherAssignments.subject', 'teacherAssignments.classroom.grade', 'teacherAssignments.academicYear']);
        return view('admin.users.show', compact('user'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,teacher,student,parent',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $user->update(['is_active' => ! $user->is_active]);
        return redirect()->route('admin.users.index')->with('success', 'User status updated.');
    }
}
