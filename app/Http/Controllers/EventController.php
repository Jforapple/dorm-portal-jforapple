<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Manages dorm worship events (one session per dorm per weekday).
 *
 * Scheduling rules are handled by the event Form Requests.
 */
class EventController extends Controller
{
    /**
     * List events newest first, grouped by dorm within a day, 20 per page.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Event::class);

        return view('events.index', [
            'events' => Event::query()
                ->orderByDesc('date')
                ->orderBy('dorm')
                ->paginate(20),
        ]);
    }

    /**
     * Show the form for planning a new worship event.
     */
    public function create(): View
    {
        Gate::authorize('create', Event::class);

        return view('events.create');
    }

    /**
     * Save a validated event and return to the list with a flash message.
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

        return view('events.show', ['event' => $event]);
    }

    /**
     * Show the form for editing a worship event.
     */
    public function edit(Event $event): View
    {
        Gate::authorize('update', $event);

        return view('events.edit', ['event' => $event]);
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
