<?php
session_start();
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['id'];

// Fetch both created and shared events, with necessary fields for calendar
$sql = "
    SELECT e.id, e.title, e.start_time AS start, e.end_time AS end, e.description, 'created' AS event_type
    FROM events e
    WHERE e.user_id = ?

    UNION

    SELECT e.id, e.title, e.start_time AS start, e.end_time AS end, e.description, 'shared' AS event_type
    FROM events e
    JOIN shared_events s ON s.event_id = e.id
    WHERE s.shared_with_user_id = ?
";

$stmt = $connect->prepare($sql);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $start = date(DATE_ATOM, strtotime($row['start']));

    if ($row['end']) {
        // Add 1 day to end date to make it inclusive for fullcalendar
        $endTimestamp = strtotime($row['end']);
        // Add 1 day (86400 seconds)
        $endPlusOne = date(DATE_ATOM, $endTimestamp + 86400);
    } else {
        // If no end date, make end = start + 1 day
        $endPlusOne = date(DATE_ATOM, strtotime($row['start']) + 86400);
    }

    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $start,
        'end' => $endPlusOne,
        'description' => $row['description'],
        'eventType' => $row['event_type'],
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
