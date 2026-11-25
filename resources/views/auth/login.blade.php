
@extends('layouts.app')

@section('title', 'Login - Dorm Portal')

@section('content')
    <div class="mx-auto max-w-md rounded-lg bg-white p-8 shadow">
        <h1 class="mb-6 text-2xl font-bold">Login</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="mb-2 block font-medium">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full rounded border border-gray-300 px-3 py-2"
                >

                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="mb-2 block font-medium">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full rounded border border-gray-300 px-3 py-2"
                >

                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700"
            >
                Login
            </button>
        </form>
    </div>
@endsection
