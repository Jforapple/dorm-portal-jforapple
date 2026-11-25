
@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                My Profile
            </h1>

            <p class="mt-1 text-sm text-gray-600">
                Update your account information and password.
            </p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        maxlength="255"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                    >

                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        value="{{ $user->email }}"
                        disabled
                        class="w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-500"
                    >

                    <p class="mt-1 text-xs text-gray-500">
                        Your email address cannot be changed here.
                    </p>
                </div>

                <div class="mb-5">
                    <label for="password" class="mb-2 block text-sm font-medium text-gray-700">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                    >

                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <p class="mt-1 text-xs text-gray-500">
                        Leave blank if you do not want to change your password.
                    </p>
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700">
                        Confirm New Password
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                    >
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-700 px-5 py-2 text-sm font-medium text-white hover:bg-blue-800"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
