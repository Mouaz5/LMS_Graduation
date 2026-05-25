<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $user->load([
            'studentProfile.classroom.grade',
            'parents',
            'children.studentProfile.classroom.grade',
            'teacherAssignments.subject',
            'teacherAssignments.classroom.grade',
            'teacherAssignments.academicYear',
        ]);

        $availableParents  = $user->role === 'student'
            ? User::where('role', 'parent')
                  ->whereNotIn('id', $user->parents->pluck('id'))
                  ->orderBy('name')->get()
            : collect();

        $availableStudents = $user->role === 'parent'
            ? User::where('role', 'student')
                  ->whereNotIn('id', $user->children->pluck('id'))
                  ->orderBy('name')->get()
            : collect();

        return view('admin.users.show', compact('user', 'availableParents', 'availableStudents'));
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

    public function linkParent(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'student', 422, 'User is not a student.');

        $request->validate([
            'parent_user_id' => 'required|exists:users,id',
            'relation'       => 'required|in:father,mother,guardian',
        ]);

        $parent = User::findOrFail($request->parent_user_id);
        abort_unless($parent->role === 'parent', 422, 'Selected user is not a parent.');

        $exists = DB::table('parent_student')
            ->where('parent_user_id', $parent->id)
            ->where('student_user_id', $user->id)
            ->exists();

        if (! $exists) {
            DB::table('parent_student')->insert([
                'parent_user_id'  => $parent->id,
                'student_user_id' => $user->id,
                'relation'        => $request->relation,
            ]);
        }

        return redirect()->route('admin.users.show', $user)
                         ->with('success', "{$parent->name} linked as {$request->relation}.");
    }

    public function unlinkParent(Request $request, User $user): RedirectResponse
    {
        $request->validate(['parent_user_id' => 'required|exists:users,id']);

        DB::table('parent_student')
            ->where('parent_user_id', $request->parent_user_id)
            ->where('student_user_id', $user->id)
            ->delete();

        return redirect()->route('admin.users.show', $user)
                         ->with('success', 'Parent unlinked.');
    }

    public function linkChild(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'parent', 422, 'User is not a parent.');

        $request->validate([
            'student_user_id' => 'required|exists:users,id',
            'relation'        => 'required|in:father,mother,guardian',
        ]);

        $student = User::findOrFail($request->student_user_id);
        abort_unless($student->role === 'student', 422, 'Selected user is not a student.');

        $exists = DB::table('parent_student')
            ->where('parent_user_id', $user->id)
            ->where('student_user_id', $student->id)
            ->exists();

        if (! $exists) {
            DB::table('parent_student')->insert([
                'parent_user_id'  => $user->id,
                'student_user_id' => $student->id,
                'relation'        => $request->relation,
            ]);
        }

        return redirect()->route('admin.users.show', $user)
                         ->with('success', "{$student->name} linked as your child.");
    }

    public function unlinkChild(Request $request, User $user): RedirectResponse
    {
        $request->validate(['student_user_id' => 'required|exists:users,id']);

        DB::table('parent_student')
            ->where('parent_user_id', $user->id)
            ->where('student_user_id', $request->student_user_id)
            ->delete();

        return redirect()->route('admin.users.show', $user)
                         ->with('success', 'Child unlinked.');
    }
}
