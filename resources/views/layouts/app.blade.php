
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dorm Portal')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 text-gray-900">

    <header class="bg-white shadow">
        <nav class="mx-auto flex max-w-6xl items-center gap-6 px-6 py-4">
            <a href="{{ route('home') }}" class="text-xl font-semibold">
                Dorm Portal
            </a>

            <a href="{{ route('roomcheck.index') }}" class="text-gray-700 hover:text-blue-600">
                Room Inspections
            </a>

            <a href="{{ route('events.index') }}" class="text-gray-700 hover:text-blue-600">
                Worship Events
            </a>
            
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="text-gray-700 hover:text-red-600"
                    >
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">
                    Login
                </a>
            @endauth

        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-8">

        @if (session('status'))
            <p class="mb-6 rounded bg-green-100 px-4 py-3 text-green-800" role="status">
                {{ session('status') }}
            </p>
        @endif

        @yield('content')

    </main>

</body>
</html>
