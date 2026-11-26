
@extends('layouts.app')

@section('title', 'Mark Worship Attendance')

@section('content')

    <div class="mb-7">
        <a
            href="{{ route('events.show', $event) }}"
            class="text-sm text-blue-700 hover:underline"
        >
            &larr; Back to event details
        </a>

        <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900">
            Mark Worship Attendance
        </h1>

        <p class="mt-2 text-gray-600">
            {{ $event->title }} —
            {{ $event->dorm->name }} —
            {{ $event->date->format('F j, Y') }}
        </p>
    </div>

    @if ($students->isEmpty())
        <div class="rounded-xl border border-gray-200 bg-white p-6 text-gray-600">
            No students are assigned to this dorm.
        </div>
    @else
        <form
            method="POST"
            action="{{ route('events.attendance.store', $event) }}"
            class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"
        >
            @csrf

            <div class="mb-5">
                <h2 class="text-lg font-semibold text-gray-900">
                    Student Attendance
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Select an attendance status for each student.
                </p>
            </div>

            @foreach ($students as $student)
                <div class="border-t border-gray-200 py-5">
                    <input
                        type="hidden"
                        name="attendances[{{ $loop->index }}][user_id]"
                        value="{{ $student->id }}"
                    >

                    <label
                        for="attendance_{{ $student->id }}"
                        class="mb-2 block font-medium text-gray-900"
                    >
                        {{ $student->name }}
                    </label>

                    <select
                        id="attendance_{{ $student->id }}"
                        name="attendances[{{ $loop->index }}][status]"
                        required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500"
                    >
                        @php
                            $currentStatus = $student->attendances->first()?->status;
                        @endphp

                        @foreach (\App\Enums\AttendanceStatus::cases() as $status)
                            <option
                                value="{{ $status->value }}"
                                @selected(
                                    old(
                                        "attendances.{$loop->parent->index}.status",
                                        $currentStatus?->value ?? 'present'
                                    ) === $status->value
                                )
                            >
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>

                    @error("attendances.{$loop->index}.status")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <div class="mt-6 border-t border-gray-200 pt-5">
                <button
                    type="submit"
                    class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800"
                >
                    Save Attendance
                </button>
            </div>
        </form>
    @endif

@endsection
