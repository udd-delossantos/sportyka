<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Approved</title>
</head>
<body>
    <h2>Hello {{ $request->user->name }},</h2>

    <p>Good news! Your booking request has been <strong>approved</strong>.</p>

    <p><strong>Booking Details:</strong></p>
    <ul>
        <li>Court: {{ $request->court->name }}</li>
        <li>Date: {{ \Carbon\Carbon::parse($request->booking_date)->format('F d, Y') }}</li>
        <li>Time: {{ \Carbon\Carbon::parse($request->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($request->end_time)->format('h:i A') }}</li>
    </ul>

    <p>We look forward to seeing you at Proving Grounds Sports Center!</p>

    <p>— Sporty Ka? Team</p>
</body>
</html>
