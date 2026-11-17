
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
                <h2 class="font-semibold">
                    Inspection details
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2">

                <div>
                    <label for="room_id" class="mb-2 block text-sm font-semibold">
                        Room ID
                    </label>

                    <input
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        type="number"
                        id="room_id"
                        name="room_id"
                        value="{{ old('room_id', $roomCheck->room_id) }}"
                        step="1"
                        required
                    >

                    @error('room_id')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="student_id" class="mb-2 block text-sm font-semibold">
                        Student ID
                    </label>

                    <input
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        type="number"
                        id="student_id"
                        name="student_id"
                        value="{{ old('student_id', $roomCheck->student_id) }}"
                        step="1"
                        required
                    >

                    @error('student_id')
                        <p class="mt-1.5 text-sm text-red-700" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="student_name" class="mb-2 block text-sm font-semibold">
                        Student Name
                    </label>

                    <input
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        type="text"
                        id="student_name"
                        name="student_name"
                        value="{{ old('student_name', $roomCheck->student_name) }}"
                        maxlength="255"
                        required
                    >

                    @error('student_name')
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
