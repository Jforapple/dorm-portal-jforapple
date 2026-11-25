
@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manage Users</h1>
                <p class="mt-1 text-sm text-gray-600">
                    View and manage Dorm Portal accounts.
                </p>
            </div>

            <a
                href="{{ route('users.create') }}"
                class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800"
            >
                Create User
            </a>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                            Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                            Email
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                            Role
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                            Dorm
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">
                            Room
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                {{ $user->name }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                {{ $user->email }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm capitalize text-gray-700">
                                {{ $user->role->value }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                {{ $user->room?->dorm?->name ?? '—' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                {{ $user->room?->number ?? '—' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                <a
                                    href="{{ route('users.show', $user) }}"
                                    class="font-medium text-blue-700 hover:text-blue-900"
                                >
                                    View
                                </a>

                                <a
                                    href="{{ route('users.edit', $user) }}"
                                    class="ml-3 font-medium text-blue-700 hover:text-blue-900"
                                >
                                    Edit
                                </a>

                                @if (auth()->id() !== $user->id)
                                    <form
                                        action="{{ route('users.destroy', $user) }}"
                                        method="POST"
                                        class="ml-3 inline"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="font-medium text-red-700 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
