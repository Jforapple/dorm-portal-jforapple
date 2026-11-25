
@extends('layouts.app')

@section('title', 'Home')

@section('content')

    <div class="py-8">

        <h1 class="text-3xl font-bold tracking-tight text-gray-900">
            Welcome to Dorm Portal
        </h1>

        <p class="mt-2 text-gray-500">
            Manage dorm room inspections and worship events.
        </p>

        <section class="mt-8" aria-labelledby="modules-heading">

            <h2 id="modules-heading" class="mb-5 text-xl font-semibold">
                Modules
            </h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                <a
                    href="{{ route('roomcheck.index') }}"
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-blue-300 hover:shadow-md"
                >
                    <h3 class="text-lg font-semibold text-gray-900">
                        Room Inspections
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        View and manage dorm room inspections.
                    </p>
                </a>

                <a
                    href="{{ route('events.index') }}"
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-blue-300 hover:shadow-md"
                >
                    <h3 class="text-lg font-semibold text-gray-900">
                        Worship Events
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        View and manage dorm worship events.
                    </p>
                </a>

            </div>

        </section>

    </div>

@endsection
