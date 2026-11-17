
@extends('layouts.app')

@section('title', 'Room Inspections')

@section('content')

    <header class="mb-7 flex flex-wrap items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                Room Inspections
            </h1>

            <p class="mt-1 text-gray-500">
                View and manage dorm room inspections.
            </p>
        </div>

        <a
            class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800"
            href="{{ route('roomcheck.create') }}"
        >
            + Add inspection
        </a>
    </header>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="border-b border-gray-200 px-6 py-5">
            <h2 class="font-semibold">
                All room inspections
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[580px] border-collapse text-left">

                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <th class="px-6 py-3">Room ID</th>
                        <th class="px-6 py-3">Student ID</th>
                        <th class="px-6 py-3">Student Name</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($rooms as $row)
                        <tr class="border-t border-gray-100 hover:bg-gray-50">
                            <td class="px-6 py-5">
                                {{ $row->room_id }}
                            </td>

                            <td class="px-6 py-5">
                                {{ $row->student_id }}
                            </td>

                            <td class="px-6 py-5">
                                {{ $row->student_name }}
                            </td>

                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <a
                                        class="text-sm font-medium text-green-700 hover:underline"
                                        href="{{ route('roomcheck.show', $row->id) }}"
                                    >
                                        View
                                    </a>
                                    <a
                                        class="text-sm font-medium text-blue-700 hover:underline"
                                        href="{{ route('roomcheck.edit', $row->id) }}"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('roomcheck.destroy', $row->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this room inspection?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="text-sm font-medium text-red-700 hover:underline"
                                            type="submit"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-14 text-center">
                                <strong>No room inspections yet.</strong>

                                <p class="my-3 text-gray-500">
                                    Add your first room inspection to get started.
                                </p>

                                <a
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800"
                                    href="{{ route('roomcheck.create') }}"
                                >
                                    Add inspection
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
        <div class="border-t border-gray-200 px-6 py-4">
            {{ $rooms->links() }}
        </div>
    </section>

@endsection
