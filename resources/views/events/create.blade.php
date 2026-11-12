
@extends('layouts.app')

@section('title', 'Create Worship Event')

@section('content')

    <div class="mx-auto max-w-3xl">

        <a class="mb-7 inline-block text-sm text-blue-700 hover:underline"
           href="{{ route('events.index') }}">
            &larr; Worship events
        </a>

        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
            Create Worship Event
        </h1>

        <p class="mb-7 mt-1 text-gray-500">
            Plan a worship session for your dorm.
        </p>

        <form class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
              action="{{ route('events.store') }}"
              method="POST"
              aria-labelledby="details-heading">

            @csrf

            <div class="border-b border-gray-200 px-6 py-5">
                <h2 id="details-heading" class="font-semibold">
                    Event details
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label for="title" class="mb-2 block text-sm font-semibold">
                        Event title
                    </label>

                    <input
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        id="title"
                        name="title"
                        value="{{ old('title', 'Worship') }}"
                        maxlength="255"
                        required
                    >

                    @error('title')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="dorm" class="mb-2 block text-sm font-semibold">
                        Dorm name
                    </label>

                    <select
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        id="dorm"
                        name="dorm"
                        required
                    >
                        <option value="">Choose a dorm</option>

                        @foreach (['Eve Hall', 'Esther Hall', 'Ruth Hall', 'Elijah Hall', 'Solomon Hall', 'Sarah Hall'] as $dorm)
                            <option value="{{ $dorm }}" @selected(old('dorm') === $dorm)>
                                {{ $dorm }}
                            </option>
                        @endforeach
                    </select>

                    @error('dorm')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="date" class="mb-2 block text-sm font-semibold">
                        Date
                    </label>

                    <input
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        type="date"
                        id="date"
                        name="date"
                        value="{{ old('date') }}"
                        required
                    >

                    @error('date')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="session" class="mb-2 block text-sm font-semibold">
                        Session
                    </label>

                    <select
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        id="session"
                        name="session"
                        required
                    >
                        <option value="">Choose a session</option>

                        <option value="morning" @selected(old('session') === 'morning')>
                            Morning (Tuesday–Friday)
                        </option>

                        <option value="evening" @selected(old('session') === 'evening')>
                            Evening (Monday)
                        </option>
                    </select>

                    @error('session')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            <div class="flex items-center justify-end gap-5 border-t border-gray-200 bg-gray-50 px-6 py-5">
                <a class="text-sm font-medium text-gray-700 hover:underline"
                   href="{{ route('events.index') }}">
                    Cancel
                </a>

                <button
                    class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800"
                    type="submit"
                >
                    Create event
                </button>
            </div>

        </form>

        <p class="mt-5 text-sm text-gray-500">
            Monday evenings &middot; Tuesday&ndash;Friday mornings &middot; No weekend check-in
        </p>

    </div>

@endsection
