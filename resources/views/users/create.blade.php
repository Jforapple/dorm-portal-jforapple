
@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <a
                href="{{ route('users.index') }}"
                class="text-sm font-medium text-blue-700 hover:text-blue-900"
            >
                &larr; Back to Users
            </a>

            <h1 class="mt-4 text-2xl font-bold text-gray-900">
                Create User
            </h1>

            <p class="mt-1 text-sm text-gray-600">
                Create a new Dorm Portal account.
            </p>
        </div>

        <form
            action="{{ route('users.store') }}"
            method="POST"
            class="space-y-5 rounded-lg border border-gray-200 bg-white p-6"
        >
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Full Name
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >

                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    minlength="8"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >

                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    required
                    minlength="8"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">
                    Role
                </label>

                <select
                    name="role"
                    id="role"
                    required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Select a role</option>
                    <option value="admin" @selected(old('role') === 'admin')>
                        Admin
                    </option>
                    <option value="staff" @selected(old('role') === 'staff')>
                        Staff
                    </option>
                    <option value="student" @selected(old('role', 'student') === 'student')>
                        Student
                    </option>
                </select>

                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="room_id" class="block text-sm font-medium text-gray-700">
                    Room Assignment
                </label>

                <select
                    name="room_id"
                    id="room_id"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">No room assigned</option>

                    @foreach ($rooms as $room)
                        <option
                            value="{{ $room->id }}"
                            @selected((string) old('room_id') === (string) $room->id)
                        >
                            {{ $room->dorm->name }} — Room {{ $room->number }}
                            ({{ $room->users()->count() }}/{{ $room->capacity }} students)
                        </option>
                    @endforeach
                </select>

                @error('room_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <p class="mt-1 text-xs text-gray-500">
                    Room assignments are only available for student accounts.
                    Rooms cannot exceed their capacity.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-5">
                <a
                    href="{{ route('users.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800"
                >
                    Create User
                </button>
            </div>
        </form>
    </div>
@endsection
