<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomCheckRequest;
use App\Models\RoomCheck;
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

        $rooms['rooms'] = RoomCheck::paginate(10);

        return view('roomchecks.index', $rooms);
    }

    /**
     * Show the form for creating a new room inspection.
     */
    public function create()
    {
        Gate::authorize('create', RoomCheck::class);

        return view('roomchecks.create');
    }

    /**
     * Validate and save a new room inspection.
     *
     * Only values from $validated are used — never read $_POST or
     * $request->all() directly, because that skips validation.
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

        return view('roomchecks.show', [
            'roomCheck' => $roomcheck,
        ]);
    }

    /**
     * Show the edit form; responds 404 when the id does not exist.
     */
    public function edit(RoomCheck $roomcheck)
    {
        Gate::authorize('update', $roomcheck);

        return view('roomchecks.edit', [
            'roomCheck' => $roomcheck,
        ]);
    }

    /**
     * Validate and update an existing room inspection.
     *
     * The record is looked up before validation so a missing id returns 404
     * rather than validation errors.
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
