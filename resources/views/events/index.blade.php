
@extends('layouts.app')

@section('title', 'Worship Events')

@section('content')

    <a class="mb-7 inline-block text-sm text-blue-700 hover:underline"
       href="{{ route('roomcheck.index') }}">
        &larr; Room inspections
    </a>

    <header class="mb-7 flex flex-wrap items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                Worship Events
            </h1>
            <p class="mt-1 text-gray-500">
                A simple overview of worship across your dorms.
            </p>
        </div>

        <a class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800"
           href="{{ route('events.create') }}">
            + Create worship event
        </a>
    </header>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
             aria-labelledby="events-heading">

        <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-6 py-5">
            <h2 id="events-heading" class="font-semibold">
                All events
            </h2>

            <span class="text-sm text-gray-500">
                {{ $events->total() }}
                {{ $events->total() === 1 ? 'event' : 'events' }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[580px] border-collapse text-left"
                   aria-label="Worship events">

                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Dorm</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Session</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($events as $event)
                        <tr class="border-t border-gray-100 hover:bg-gray-50">
                            <td class="px-6 py-5 font-semibold text-gray-800">
                                {{ $event->title }}
                            </td>

                            <td class="px-6 py-5 text-sm">
                                {{ $event->dorm }}
                            </td>

                            <td class="px-6 py-5 text-sm">
                                {{ $event->date->format('M j, Y') }}
                                <span class="mt-1 block text-xs text-gray-500">
                                    {{ $event->date->format('l') }}
                                </span>
                            </td>

                            <td class="px-6 py-5">
                                <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $event->session === 'morning'
                                        ? 'bg-amber-100 text-amber-800'
                                        : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($event->session) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-14 text-center">
                                <strong>No worship events yet.</strong>

                                <p class="my-3 text-gray-500">
                                    Create your first event to start organizing dorm worship.
                                </p>

                                <a class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800"
                                   href="{{ route('events.create') }}">
                                    Create worship event
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        @if ($events->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $events->links('pagination::simple-tailwind') }}
            </div>
        @endif

    </section>

    <p class="mt-5 text-sm text-gray-500">
        Monday evenings &middot; Tuesday&ndash;Friday mornings &middot; No weekend check-in
    </p>

@endsection
