
@extends('layouts.app')

@section('title', 'My Worship Attendance')

@section('content')

    <div class="mb-7">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
            My Worship Attendance
        </h1>

        <p class="mt-2 text-gray-600">
            View your worship attendance percentage and history.
        </p>
    </div>

    <div class="mb-7 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-medium text-gray-500">
            Attendance Percentage
        </p>

        <p class="mt-2 text-4xl font-bold text-blue-700">
            {{ number_format($attendancePercentage, 2) }}%
        </p>

        <p class="mt-2 text-sm text-gray-500">
            Present and Late count as attended. Excused records are excluded
            from the calculation.
        </p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-5 text-xl font-semibold text-gray-900">
            Attendance History
        </h2>

        @if ($attendances->isEmpty())
            <p class="text-gray-600">
                You do not have any worship attendance records yet.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-200 text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Event</th>
                            <th class="px-4 py-3 font-medium">Date</th>
                            <th class="px-4 py-3 font-medium">Session</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach ($attendances as $attendance)
                            <tr>
                                <td class="px-4 py-4 font-medium text-gray-900">
                                    {{ $attendance->event->title }}
                                </td>

                                <td class="px-4 py-4 text-gray-600">
                                    {{ $attendance->event->date->format('F j, Y') }}
                                </td>

                                <td class="px-4 py-4 text-gray-600">
                                    {{ ucfirst($attendance->event->session) }}
                                </td>

                                <td class="px-4 py-4">
                                    <span class="font-medium text-gray-900">
                                        {{ $attendance->status->name }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
