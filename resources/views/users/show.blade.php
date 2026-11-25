
@extends('layouts.app')

@section('title', 'User Details')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <a
                href="{{ route('users.index') }}"
                class="text-sm font-medium text-blue-700 hover:text-blue-900"
            >
                &larr; Back to Users
            </a>

            <div class="mt-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        User Details
                    </h1>

                    <p class="mt-1 text-sm text-gray-600">
                        View account information.
                    </p>
                </div>

                <a
                    href="{{ route('users.edit', $user) }}"
                    class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800"
                >
                    Edit User
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <dl class="divide-y divide-gray-200">
                <div class="px-6 py-4">
                    <dt class="text-sm font-medium text-gray-500">
                        Full Name
                    </dt>

                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->name }}
                    </dd>
                </div>

                <div class="px-6 py-4">
                    <dt class="text-sm font-medium text-gray-500">
                        Email Address
                    </dt>

                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->email }}
                    </dd>
                </div>

                <div class="px-6 py-4">
                    <dt class="text-sm font-medium text-gray-500">
                        Role
                    </dt>

                    <dd class="mt-1 text-sm capitalize text-gray-900">
                        {{ $user->role->value }}
                    </dd>
                </div>

                <div class="px-6 py-4">
                    <dt class="text-sm font-medium text-gray-500">
                        Dorm
                    </dt>

                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->room?->dorm?->name ?? 'No dorm assigned' }}
                    </dd>
                </div>

                <div class="px-6 py-4">
                    <dt class="text-sm font-medium text-gray-500">
                        Room
                    </dt>

                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->room?->number ?? 'No room assigned' }}
                    </dd>
                </div>

                <div class="px-6 py-4">
                    <dt class="text-sm font-medium text-gray-500">
                        Account Created
                    </dt>

                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->created_at->format('d M Y, H:i') }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
