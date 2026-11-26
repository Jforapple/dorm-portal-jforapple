
@extends('layouts.app')

@section('title', 'Worship Event Details')

@section('content')

    <a
        href="{{ route('events.index') }}"
        class="mb-7 inline-block text-sm text-blue-700 hover:underline"
    >
        &larr; Back to worship events
    </a>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
            {{ $event->title }}
        </h1>

        <dl class="mt-6 space-y-5">

            <div>
                <dt class="text-sm font-medium text-gray-500">Dorm</dt>
                <dd class="mt-1 text-gray-900">{{ $event->dorm->name }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Date</dt>
                <dd class="mt-1 text-gray-900">
                    {{ $event->date->format('F j, Y') }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Day</dt>
                <dd class="mt-1 text-gray-900">
                    {{ $event->date->format('l') }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Session</dt>
                <dd class="mt-1 text-gray-900">
                    {{ ucfirst($event->session) }}
                </dd>
            </div>

        </dl>
        
        @if (auth()->user()->role === \App\Enums\Role::Staff)
            <div class="mt-7 border-t border-gray-200 pt-5">
                <a
                    href="{{ route('events.attendance', $event) }}"
                    class="inline-flex items-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800"
                >
                    Mark Attendance
                </a>
            </div>
        @endif

    </div>

@endsection
