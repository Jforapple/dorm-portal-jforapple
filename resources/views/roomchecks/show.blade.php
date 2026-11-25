
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
                <dt class="text-sm font-medium text-gray-500">Dorm</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $roomCheck->room->dorm->name }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Room</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $roomCheck->room->number }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Student Name</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $roomCheck->student->name }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Student ID</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $roomCheck->student_id }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Inspection Date</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $roomCheck->inspection_date?->format('d M Y') ?? 'Not recorded' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Inspector</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $roomCheck->inspector?->name ?? 'Not recorded' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Overall Score</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $roomCheck->score !== null ? $roomCheck->score . '/100' : 'Not recorded' }}
                </dd>
            </div>

            <div>
                <dt class="mb-3 text-sm font-medium text-gray-500">
                    Inspection Checklist
                </dt>

                <dd>
                    <ul class="space-y-2">
                        @foreach ([
                            'bed' => 'Bed',
                            'floor' => 'Floor',
                            'desk' => 'Desk',
                            'trash' => 'Trash',
                            'bathroom' => 'Bathroom',
                        ] as $key => $label)
                            <li class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                                <span class="font-medium text-gray-700">
                                    {{ $label }}
                                </span>

                                @php
                                    $result = $roomCheck->checklist[$key] ?? null;
                                @endphp

                                @if ($result === 'pass')
                                    <span class="font-semibold text-green-700">Pass</span>
                                @elseif ($result === 'fail')
                                    <span class="font-semibold text-red-700">Fail</span>
                                @else
                                    <span class="text-gray-500">Not recorded</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="mt-1 whitespace-pre-line text-gray-900">
                    {{ $roomCheck->notes ?: 'No notes recorded.' }}
                </dd>
            </div>
        </dl>

        <div class="mt-8 flex flex-wrap gap-4">
            @can('update', $roomCheck)
                <a
                    class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800"
                    href="{{ route('roomcheck.edit', $roomCheck->id) }}"
                >
                    Edit inspection
                </a>
            @endcan

            <a
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                href="{{ route('roomcheck.index') }}"
            >
                Back to inspections
            </a>
        </div>
    </section>
@endsection
