<?php

require_once __DIR__ . '/../config/db.php';
class EventController
{
    public static function addEvent($title, $description, $datetime, $endDatetime, $location, $sharedWithEmail = null)
    {
        global $connect;

        
        if (!isset($_SESSION['id'])) {
            return "Unauthorized";
        }

        if (empty($title) || empty($description) || empty($datetime) || empty($endDatetime) || empty($location)) {
            return "Please fill in all required fields.";
        } elseif (strtotime($endDatetime) <= strtotime($datetime)) {
            return "End time must be after start time.";
        }

        $userId = $_SESSION['id'];

        // Use start_time for the datetime and end_time for the end datetime
        $stmt = $connect->prepare("INSERT INTO events (user_id, title, description, start_time, end_time, location) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $userId, $title, $description, $datetime, $endDatetime, $location);
        if (!$stmt->execute()) {
            return "Failed to create event.";
        }

        $eventId = $stmt->insert_id;

        // If shared_with email is provided, find user id
        if ($sharedWithEmail) {
            $stmtUser = $connect->prepare("SELECT id FROM users WHERE email = ?");
            $stmtUser->bind_param("s", $sharedWithEmail);
            $stmtUser->execute();
            $resultUser = $stmtUser->get_result();

            if ($resultUser->num_rows > 0) {
                $sharedUserId = $resultUser->fetch_assoc()['id'];

                // Save into shared_events
                $stmtShare = $connect->prepare("INSERT INTO shared_events (event_id, shared_with_user_id) VALUES (?, ?)");
                $stmtShare->bind_param("ii", $eventId, $sharedUserId);
                $stmtShare->execute();
            }
        }

        return "Event created successfully.";
    }
}
?>