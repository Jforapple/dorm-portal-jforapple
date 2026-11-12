<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorm Portal</title>
</head>
<body>
    <h1>Welcome to Dorm Portal</h1>

    <h2>Modules</h2>

    <ul>
        <li>
            <a href="{{ route('roomcheck.index') }}">Room Inspections</a>
        </li>
        <li>
            <a href="{{ route('events.index') }}">Worship Events</a>
        </li>
    </ul>
</body>
</html>
