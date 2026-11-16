<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomCheckRequest;
use App\Models\RoomCheck;

/**
 * CRUD for room inspections (/roomcheck).
 *
 * This is the oldest module and is intentionally written the "long way"
 * (inline validation, manual property assignment, string ids) so you can
 * compare it with EventController and refactor it.
 *
 * TODO: Move validation into a FormRequest (php artisan make:request RoomCheckRequest).
 * TODO: Use route model binding (show(RoomCheck $roomcheck)) instead of findOrFail().
 * TODO: Add return types, implement show(), and paginate index().
 */
class RoomCheckController extends Controller
{
    /**
     * List every room inspection.
     *
     * TODO: RoomCheck::all() loads the whole table — paginate instead.
     */
    public function index()
    {
        $rooms['rooms'] = RoomCheck::all();

        return view('roomchecks.index', $rooms);
    }

    /**
     * Show the form for creating a new room inspection.
     */
    public function create()
    {
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
        RoomCheck::create($request->validated());

        return redirect()->route('roomcheck.index');
    }

    /**
     * Display a single room inspection.
     *
     * TODO: Not implemented yet — the route exists but returns an empty page.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the edit form; responds 404 when the id does not exist.
     */
    public function edit(string $id)
    {
        $roomCheck = RoomCheck::findOrFail($id);

        return view('roomchecks.edit', compact('roomCheck'));
    }

    /**
     * Validate and update an existing room inspection.
     *
     * The record is looked up before validation so a missing id returns 404
     * rather than validation errors.
     */
    public function update(RoomCheckRequest $request, string $id)
    {
        $roomCheck = RoomCheck::findOrFail($id);

        $roomCheck->update($request->validated());

        return redirect()->route('roomcheck.index');
    }

    /**
     * Delete one room inspection and return to the list.
     */
    public function destroy(string $id)
    {
        $roomCheck = RoomCheck::findOrFail($id);
        $roomCheck->delete();

        return redirect()->route('roomcheck.index');
    }
}
