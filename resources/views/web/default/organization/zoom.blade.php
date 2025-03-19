<!DOCTYPE html>
<html>
<head>
    <title>Create Zoom Meeting</title>
</head>
<body>
    <h1>Create a Zoom Meeting</h1>
    <form action="{{ route('zoom.meeting.create') }}" method="POST">
        @csrf
        <label for="topic">Meeting Topic:</label>
        <input type="text" name="topic" id="topic" placeholder="Meeting Topic" required>
        
        <label for="start_time">Start Time:</label>
        <input type="datetime-local" name="start_time" id="start_time" required>
        
        <label for="duration">Duration (minutes):</label>
        <input type="number" name="duration" id="duration" placeholder="Duration in minutes" required>
        
        <label for="agenda">Agenda:</label>
        <textarea name="agenda" id="agenda" placeholder="Meeting Agenda"></textarea>
        
        <button type="submit">Create Meeting</button>
    </form>
</body>
</html>