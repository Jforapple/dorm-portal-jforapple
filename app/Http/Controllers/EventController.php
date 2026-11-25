<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Dorm;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * List events newest first, grouped by dorm within a day.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Event::class);

        return view('events.index', [
            'events' => Event::with('dorm')
                ->orderByDesc('date')
                ->orderBy('dorm_id')
                ->paginate(20),
        ]);
    }

    /**
     * Show the form for planning a new worship event.
     */
    public function create(): View
    {
        Gate::authorize('create', Event::class);

        return view('events.create', [
            'dorms' => Dorm::orderBy('name')->get(),
        ]);
    }

    /**
     * Save a validated event.
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        Gate::authorize('create', Event::class);

        Event::create($request->validated());

        return redirect()
            ->route('events.index')
            ->with('status', 'Worship event created.');
    }

    /**
     * Display the details of a worship event.
     */
    public function show(Event $event): View
    {
        Gate::authorize('view', $event);

        $event->load('dorm');

        return view('events.show', ['event' => $event]);
    }

    /**
     * Show the form for editing a worship event.
     */
    public function edit(Event $event): View
    {
        Gate::authorize('update', $event);

        return view('events.edit', [
            'event' => $event,
            'dorms' => Dorm::orderBy('name')->get(),
        ]);
    }

    /**
     * Update an existing worship event.
     */
    public function update(
        UpdateEventRequest $request,
        Event $event
    ): RedirectResponse {
        Gate::authorize('update', $event);

        $event->update($request->validated());

        return redirect()
            ->route('events.index')
            ->with('status', 'Worship event updated.');
    }

    /**
     * Delete a worship event.
     */
    public function destroy(Event $event): RedirectResponse
    {
        Gate::authorize('delete', $event);

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('status', 'Worship event deleted.');
    }
}
