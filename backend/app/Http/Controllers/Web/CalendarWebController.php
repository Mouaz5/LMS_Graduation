<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolCalendar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarWebController extends Controller
{
    public function index(): View
    {
        $events = SchoolCalendar::orderBy('date')->get();
        return view('admin.calendar.index', compact('events'));
    }

    public function show(SchoolCalendar $event): View
    {
        $event->load('school');
        return view('admin.calendar.show', compact('event'));
    }

    public function create(): View
    {
        return view('admin.calendar.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:holiday,event,exam',
            'description' => 'required|string|max:500',
        ]);

        $school = School::first();

        SchoolCalendar::create([
            'school_id' => $school->id,
            'date' => $request->date,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.calendar.index')->with('success', 'Calendar event created successfully.');
    }
}
