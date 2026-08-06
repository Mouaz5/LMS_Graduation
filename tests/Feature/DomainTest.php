<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\School;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\TeacherSubjectClassroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DomainTest extends TestCase
{
    use RefreshDatabase;

    private User       $admin;
    private User       $teacher;
    private School     $school;
    private AcademicYear $year;
    private Classroom  $classroom;
    private Subject    $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin   = User::factory()->create(['role' => 'admin']);
        $this->teacher = User::factory()->create(['role' => 'teacher']);

        $this->school = School::create([
            'name'    => 'Test School',
            'address' => '1 Test St',
            'phone'   => '+9621234567',
        ]);

        $this->year = AcademicYear::create([
            'school_id'  => $this->school->id,
            'name'       => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date'   => '2026-06-30',
            'is_active'  => true,
        ]);

        $grade = Grade::create([
            'school_id'   => $this->school->id,
            'name'        => 'Grade 7',
            'order_index' => 1,
        ]);

        $this->classroom = Classroom::create([
            'grade_id' => $grade->id,
            'name'     => '7-A',
            'capacity' => 30,
        ]);

        $this->subject = Subject::create([
            'school_id' => $this->school->id,
            'name'      => 'Mathematics',
            'code'      => 'MATH',
        ]);

        TeacherSubjectClassroom::create([
            'teacher_user_id'  => $this->teacher->id,
            'subject_id'       => $this->subject->id,
            'classroom_id'     => $this->classroom->id,
            'academic_year_id' => $this->year->id,
        ]);
    }

    // ---------------------------------------------------------------
    // UC-33: Academic Years & Semesters
    // ---------------------------------------------------------------

    public function test_admin_can_create_academic_year(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/academic-years', [
            'school_id'  => $this->school->id,
            'name'       => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date'   => '2027-06-30',
        ])->assertStatus(201)
           ->assertJsonPath('name', '2026-2027');
    }

    public function test_admin_can_create_semester(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/semesters', [
            'academic_year_id' => $this->year->id,
            'name'             => 'Fall Semester',
            'start_date'       => '2025-09-01',
            'end_date'         => '2026-01-31',
        ])->assertStatus(201)
           ->assertJsonPath('name', 'Fall Semester');
    }

    public function test_non_admin_cannot_create_academic_year(): void
    {
        Sanctum::actingAs($this->teacher);

        $this->postJson('/api/academic-years', [
            'school_id'  => $this->school->id,
            'name'       => '2026-2027',
            'start_date' => '2026-09-01',
            'end_date'   => '2027-06-30',
        ])->assertStatus(403);
    }

    // ---------------------------------------------------------------
    // UC-07: Teacher assignment to classroom
    // ---------------------------------------------------------------

    public function test_admin_can_assign_teacher_to_classroom(): void
    {
        Sanctum::actingAs($this->admin);

        $newTeacher = User::factory()->create(['role' => 'teacher']);

        $this->postJson('/api/teacher-assignments', [
            'teacher_user_id'  => $newTeacher->id,
            'subject_id'       => $this->subject->id,
            'classroom_id'     => $this->classroom->id,
            'academic_year_id' => $this->year->id,
        ])->assertStatus(201);

        $this->assertDatabaseHas('teacher_subject_classroom', [
            'teacher_user_id' => $newTeacher->id,
            'classroom_id'    => $this->classroom->id,
        ]);
    }

    // ---------------------------------------------------------------
    // UC-06: Parent-student linking
    // ---------------------------------------------------------------

    public function test_admin_can_link_student_to_parent(): void
    {
        Sanctum::actingAs($this->admin);

        $parent  = User::factory()->create(['role' => 'parent']);
        $student = User::factory()->create(['role' => 'student']);

        $this->postJson('/api/parent-student', [
            'parent_user_id'  => $parent->id,
            'student_user_id' => $student->id,
            'relation'        => 'mother',
        ])->assertStatus(201);

        $this->assertDatabaseHas('parent_student', [
            'parent_user_id'  => $parent->id,
            'student_user_id' => $student->id,
        ]);
    }

    public function test_linking_non_parent_user_returns_422(): void
    {
        Sanctum::actingAs($this->admin);

        $student  = User::factory()->create(['role' => 'student']);
        $notParent = User::factory()->create(['role' => 'teacher']);

        $this->postJson('/api/parent-student', [
            'parent_user_id'  => $notParent->id,
            'student_user_id' => $student->id,
            'relation'        => 'father',
        ])->assertStatus(422);
    }

    // ---------------------------------------------------------------
    // UC-10: School calendar
    // ---------------------------------------------------------------

    public function test_admin_can_add_calendar_event(): void
    {
        Sanctum::actingAs($this->admin);

        $this->postJson('/api/school-calendar', [
            'school_id'   => $this->school->id,
            'date'        => '2026-12-25',
            'type'        => 'holiday',
            'description' => 'Christmas Break',
        ])->assertStatus(201);
    }

    public function test_authenticated_user_can_view_calendar(): void
    {
        Sanctum::actingAs($this->teacher);

        $this->getJson('/api/school-calendar')
             ->assertStatus(200);
    }

    // ---------------------------------------------------------------
    // Classrooms: teacher-filtered visibility
    // ---------------------------------------------------------------

    public function test_teacher_gets_only_assigned_classrooms(): void
    {
        // Create a second classroom the teacher is NOT assigned to
        $grade2 = Grade::create([
            'school_id'   => $this->school->id,
            'name'        => 'Grade 8',
            'order_index' => 2,
        ]);
        Classroom::create(['grade_id' => $grade2->id, 'name' => '8-A', 'capacity' => 30]);

        Sanctum::actingAs($this->teacher);

        $response = $this->getJson('/api/classrooms');
        $response->assertStatus(200);

        $ids = collect($response->json())->pluck('id')->toArray();
        $this->assertContains($this->classroom->id, $ids);
        $this->assertCount(1, $ids);
    }

    public function test_admin_gets_all_classrooms(): void
    {
        Sanctum::actingAs($this->admin);

        $this->getJson('/api/classrooms')
             ->assertStatus(200)
             ->assertJsonCount(1); // only what setUp created
    }
}
