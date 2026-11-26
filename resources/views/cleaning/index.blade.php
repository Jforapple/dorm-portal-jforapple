
@extends('layouts.app')

@section('title', 'Cleaning Duties')

@section('content')
    <div class="mb-7 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                Cleaning Duties
            </h1>
            <p class="mt-2 text-gray-600">
                View assigned cleaning duties and their completion status.
            </p>
        </div>

        @if (auth()->user()->role === \App\Enums\Role::Staff)
            <a href="{{ route('cleaning-duties.create') }}"
               class="rounded-lg bg-blue-600 px-5 py-3 font-medium text-white hover:bg-blue-700">
                Assign Cleaning Duty
            </a>
        @endif
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-lg bg-green-100 p-4 text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Area
                        </th>
                        @if (auth()->user()->role !== \App\Enums\Role::Student)
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                                Student
                            </th>
                        @endif
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Due Date
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($duties as $duty)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $duty->area }}
                            </td>

                            @if (auth()->user()->role !== \App\Enums\Role::Student)
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $duty->student->name }}
                                </td>
                            @endif

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $duty->due_date->format('d M Y') }}
                            </td>

                            <td class="px-6 py-4 text-sm">
                                @if ($duty->completed_at)
                                    <span class="rounded-full bg-green-100 px-3 py-1 font-medium text-green-800">
                                        Completed
                                    </span>
                                @elseif ($duty->isOverdue())
                                    <span class="rounded-full bg-red-100 px-3 py-1 font-medium text-red-800">
                                        Overdue
                                    </span>
                                @else
                                    <span class="rounded-full bg-yellow-100 px-3 py-1 font-medium text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm">
                                @if (auth()->user()->role === \App\Enums\Role::Student
                                    && ! $duty->completed_at)
                                    <form method="POST"
                                          action="{{ route('cleaning-duties.complete', $duty) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="font-medium text-blue-600 hover:text-blue-800">
                                            Mark as Done
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === \App\Enums\Role::Student ? 4 : 5 }}"
                                class="px-6 py-8 text-center text-gray-500">
                                No cleaning duties found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
