<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Enums\Role;
use App\Http\Requests\MarkAttendanceRequest;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class WorshipController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Attendance::class);

        $user = auth()->user();

        if ($user->role !== Role::Student) {
            return redirect()->route('events.index');
        }

        $attendances = $user->attendances()
            ->with('event')
            ->latest()
            ->get();

        $attended = $attendances->filter(
            fn (Attendance $attendance) => in_array(
                $attendance->status,
                [AttendanceStatus::Present, AttendanceStatus::Late],
                true
            )
        )->count();

        $eligible = $attendances->filter(
            fn (Attendance $attendance) => $attendance->status !== AttendanceStatus::Excused
        )->count();

        $attendancePercentage = $eligible > 0
            ? round(($attended / $eligible) * 100, 2)
            : 0;

        return view('attendance.index', compact(
            'attendances',
            'attendancePercentage'
        ));
    }

    public function show(Event $event)
    {
        Gate::authorize('create', Attendance::class);

        $students = User::query()
            ->where('role', Role::Student)
            ->whereHas('room', function (Builder $query) use ($event) {
                $query->where('dorm_id', $event->dorm_id);
            })
            ->with(['attendances' => function ($query) use ($event) {
                $query->where('event_id', $event->id);
            }])
            ->orderBy('name')
            ->get();

        return view('attendance.show', compact('event', 'students'));
    }

    public function store(MarkAttendanceRequest $request, Event $event)
    {
        $validated = $request->validated();

        $submittedIds = collect($validated['attendances'])
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values();

        $eligibleIds = User::query()
            ->where('role', Role::Student)
            ->whereHas('room', function (Builder $query) use ($event) {
                $query->where('dorm_id', $event->dorm_id);
            })
            ->pluck('id')
            ->sort()
            ->values();

        if ($eligibleIds->diff($submittedIds)->isNotEmpty()
            || $submittedIds->diff($eligibleIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'attendances' => 'Attendance must be submitted for every student in the event dorm.',
            ]);
        }

        foreach ($validated['attendances'] as $entry) {
            $event->attendances()->updateOrCreate(
                ['user_id' => $entry['user_id']],
                ['status' => $entry['status']]
            );
        }

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Worship attendance saved.');
    }
}
