
@extends('layouts.app')

@section('title', 'Create Room Inspection')

@section('content')

    <div class="mx-auto max-w-3xl">

        <a
            class="mb-7 inline-block text-sm text-blue-700 hover:underline"
            href="{{ route('roomcheck.index') }}"
        >
            &larr; Room inspections
        </a>

        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
            Create Room Inspection
        </h1>

        <p class="mb-7 mt-1 text-gray-500">
            Record a dorm room inspection.
        </p>

        <form
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            action="{{ route('roomcheck.store') }}"
            method="POST"
        >

            @csrf

            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold">
                    Inspection details
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6">

                <div>
                    <label for="room_id" class="mb-2 block text-sm font-semibold">
                        Room
                    </label>

                    <select
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        id="room_id"
                        name="room_id"
                        required
                    >
                        <option value="">Select a room</option>

                        @foreach ($availableRooms as $room)
                            <option
                                value="{{ $room->id }}"
                                @selected(old('room_id') == $room->id)
                            >
                                {{ $room->dorm->name }} - Room {{ $room->number }}
                            </option>
                        @endforeach
                    </select>

                    @error('room_id')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="student_id" class="mb-2 block text-sm font-semibold">
                        Student
                    </label>

                    <select
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        id="student_id"
                        name="student_id"
                        required
                    >
                        <option value="">Select a student</option>

                        @foreach ($students as $student)
                            @if ($student->room_id !== null)
                                <option
                                    value="{{ $student->id }}"
                                    @selected(old('student_id') == $student->id)
                                >
                                    {{ $student->name }} (ID: {{ $student->id }})
                                    - Room {{ $student->room_id }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                    @error('student_id')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            <div class="flex items-center justify-end gap-5 border-t border-gray-200 bg-gray-50 px-6 py-5">
                <a
                    class="text-sm font-medium text-gray-700 hover:underline"
                    href="{{ route('roomcheck.index') }}"
                >
                    Cancel
                </a>

                <button
                    class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800"
                    type="submit"
                >
                    Save Room Inspection
                </button>
            </div>

        </form>

    </div>

@endsection
