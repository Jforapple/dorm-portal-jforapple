
@extends('layouts.app')

@section('title', 'Assign Cleaning Duty')

@section('content')
    <div class="mb-7">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
            Assign Cleaning Duty
        </h1>
        <p class="mt-2 text-gray-600">
            Assign a cleaning task to a student and set its due date.
        </p>
    </div>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6">
        <form method="POST" action="{{ route('cleaning-duties.store') }}">
            @csrf

            <div class="mb-5">
                <label for="area" class="mb-2 block text-sm font-medium text-gray-700">
                    Cleaning Area
                </label>

                <input
                    type="text"
                    name="area"
                    id="area"
                    value="{{ old('area') }}"
                    required
                    maxlength="255"
                    placeholder="e.g. Dormitory hallway or Room 101"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                >

                @error('area')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="user_id" class="mb-2 block text-sm font-medium text-gray-700">
                    Assign to Student
                </label>

                <select
                    name="user_id"
                    id="user_id"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Select a student</option>

                    @foreach ($students as $student)
                        <option
                            value="{{ $student->id }}"
                            @selected(old('user_id') == $student->id)
                        >
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>

                @error('user_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="due_date" class="mb-2 block text-sm font-medium text-gray-700">
                    Due Date
                </label>

                <input
                    type="date"
                    name="due_date"
                    id="due_date"
                    value="{{ old('due_date') }}"
                    required
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-blue-500 focus:ring-blue-500"
                >

                @error('due_date')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-3">
                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-3 font-medium text-white hover:bg-blue-700"
                >
                    Assign Duty
                </button>

                <a
                    href="{{ route('cleaning-duties.index') }}"
                    class="rounded-lg border border-gray-300 px-5 py-3 font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
