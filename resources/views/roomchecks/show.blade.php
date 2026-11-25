
@extends('layouts.app')

@section('title', 'Inspection Details')

@section('content')

    <header class="mb-7">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
            Inspection Details
        </h1>

        <p class="mt-1 text-gray-500">
            View the details of this room inspection.
        </p>
    </header>

    <section class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

        <dl class="space-y-5">
            <div>
                <dt class="text-sm font-medium text-gray-500">
                    Room ID
                </dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $roomCheck->room_id }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">
                    Student ID
                </dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $roomCheck->student_id }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">
                    Student Name
                </dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $roomCheck->student_name }}
                </dd>
            </div>
        </dl>

        <div class="mt-8 flex flex-wrap gap-4">
            <a
                class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800"
                href="{{ route('roomcheck.edit', $roomCheck->id) }}"
            >
                Edit inspection
            </a>

            <a
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                href="{{ route('roomcheck.index') }}"
            >
                Back to inspections
            </a>
        </div>

    </section>

@endsection
