
@extends('layouts.app')

@section('title', 'Edit Room Inspection')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a
            class="mb-7 inline-block text-sm text-blue-700 hover:underline"
            href="{{ route('roomcheck.index') }}"
        >
            &larr; Room inspections
        </a>

        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
            Edit Room Inspection
        </h1>

        <p class="mb-7 mt-1 text-gray-500">
            Update the details of this room inspection.
        </p>

        <form
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
            action="{{ route('roomcheck.update', $roomCheck->id) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="border-b border-gray-200 px-6 py-5">
                <h2 class="font-semibold">Inspection details</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6">
                <div>
                    <label for="room_id" class="mb-2 block text-sm font-semibold">
                        Room
                    </label>

                    <select
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
                        id="room_id"
                        name="room_id"
                        required
                    >
                        <option value="">Select a room</option>

                        @foreach ($availableRooms as $room)
                            <option
                                value="{{ $room->id }}"
                                @selected(old('room_id', $roomCheck->room_id) == $room->id)
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
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
                        id="student_id"
                        name="student_id"
                        required
                    >
                        <option value="">Select a student</option>

                        @foreach ($students as $student)
                            @if ($student->room_id !== null)
                                <option
                                    value="{{ $student->id }}"
                                    @selected(old('student_id', $roomCheck->student_id) == $student->id)
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

                <div>
                    <label for="inspection_date" class="mb-2 block text-sm font-semibold">
                        Inspection Date
                    </label>

                    <input
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
                        id="inspection_date"
                        name="inspection_date"
                        type="date"
                        value="{{ old('inspection_date', $roomCheck->inspection_date?->format('Y-m-d')) }}"
                        required
                    >

                    @error('inspection_date')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <h2 class="mb-3 font-semibold">Inspection checklist</h2>

                    @foreach (['bed' => 'Bed', 'floor' => 'Floor', 'desk' => 'Desk', 'trash' => 'Trash', 'bathroom' => 'Bathroom'] as $key => $label)
                        <div class="mb-4">
                            <label
                                for="checklist_{{ $key }}"
                                class="mb-2 block text-sm font-medium"
                            >
                                {{ $label }}
                            </label>

                            <select
                                class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
                                id="checklist_{{ $key }}"
                                name="checklist[{{ $key }}]"
                                required
                            >
                                <option value="">Select result</option>
                                <option
                                    value="pass"
                                    @selected(old("checklist.$key", $roomCheck->checklist[$key] ?? '') === 'pass')
                                >
                                    Pass
                                </option>
                                <option
                                    value="fail"
                                    @selected(old("checklist.$key", $roomCheck->checklist[$key] ?? '') === 'fail')
                                >
                                    Fail
                                </option>
                            </select>

                            @error("checklist.$key")
                                <p class="mt-1.5 text-sm text-red-700" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endforeach

                    @error('checklist')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="score" class="mb-2 block text-sm font-semibold">
                        Overall Score (0–100)
                    </label>

                    <input
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
                        id="score"
                        name="score"
                        type="number"
                        min="0"
                        max="100"
                        value="{{ old('score', $roomCheck->score) }}"
                        required
                    >

                    @error('score')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="mb-2 block text-sm font-semibold">
                        Notes (Optional)
                    </label>

                    <textarea
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm"
                        id="notes"
                        name="notes"
                        rows="4"
                    >{{ old('notes', $roomCheck->notes) }}</textarea>

                    @error('notes')
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
                    Update Room Inspection
                </button>
            </div>
        </form>
    </div>
@endsection
