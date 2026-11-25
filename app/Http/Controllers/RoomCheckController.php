<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomCheckRequest;
use App\Models\Room;
use App\Models\RoomCheck;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * CRUD for room inspections (/roomcheck).
 */
class RoomCheckController extends Controller
{
    /**
     * List room inspections with pagination.
     */
    public function index()
    {
        Gate::authorize('viewAny', RoomCheck::class);

        $rooms = RoomCheck::with(['room.dorm', 'student'])
            ->paginate(10);

        return view('roomchecks.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room inspection.
     */
    public function create()
    {
        Gate::authorize('create', RoomCheck::class);

        return view('roomchecks.create', [
            'availableRooms' => Room::with('dorm')
                ->orderBy('number')
                ->get(),

            'students' => User::where('role', 'student')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Validate and save a new room inspection.
     */
    public function store(RoomCheckRequest $request)
    {
        Gate::authorize('create', RoomCheck::class);

        RoomCheck::create($request->validated());

        return redirect()->route('roomcheck.index');
    }

    /**
     * Display a single room inspection.
     */
    public function show(RoomCheck $roomcheck)
    {
        Gate::authorize('view', $roomcheck);

        $roomcheck->load(['room.dorm', 'student']);

        return view('roomchecks.show', [
            'roomCheck' => $roomcheck,
        ]);
    }

    /**
     * Show the edit form.
     */
    public function edit(RoomCheck $roomcheck)
    {
        Gate::authorize('update', $roomcheck);

        return view('roomchecks.edit', [
            'roomCheck' => $roomcheck,

            'availableRooms' => Room::with('dorm')
                ->orderBy('number')
                ->get(),

            'students' => User::where('role', 'student')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Validate and update an existing room inspection.
     */
    public function update(RoomCheckRequest $request, RoomCheck $roomcheck)
    {
        Gate::authorize('update', $roomcheck);

        $roomcheck->update($request->validated());

        return redirect()->route('roomcheck.index');
    }

    /**
     * Delete one room inspection and return to the list.
     */
    public function destroy(RoomCheck $roomcheck)
    {
        Gate::authorize('delete', $roomcheck);

        $roomcheck->delete();

        return redirect()->route('roomcheck.index');
    }
}
