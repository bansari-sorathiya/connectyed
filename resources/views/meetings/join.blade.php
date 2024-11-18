<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Meeting</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container mx-auto my-5">
        <h2 class="text-xl font-bold mb-4">Join Meeting</h2>
        
        <div class="meeting-details">
            <h3 class="text-lg font-semibold">Meeting ID: {{ $meeting['id'] }}</h3>
            <p>Title: {{ $meeting['title'] }}</p>
            <p>Start Time: {{ \Carbon\Carbon::parse($meeting['start_time'])->format('Y-m-d H:i:s') }}</p>
            <p>Duration: {{ $meeting['duration'] }} minutes</p>

            @if ($meeting['google_meet_link'])
                <div class="mt-4">
                    <a href="{{ $meeting['google_meet_link'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Join Google Meet</a>
                </div>
            @else
                <div class="mt-4">
                    <p>No meeting link available. Please check back later.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
