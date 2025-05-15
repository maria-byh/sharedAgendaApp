<?php
// Include the database connection
require_once __DIR__ . '/../config/db.php';

class Event
{
    public static function getUserEvents($userId)
    {
        global $connect;
        $sql = "
            SELECT e.*, 
            GROUP_CONCAT(u.email SEPARATOR ', ') AS shared_with
            FROM events e
            LEFT JOIN shared_events s ON e.id = s.event_id
            LEFT JOIN users u ON s.shared_with_user_id = u.id
            WHERE e.user_id = ?
            GROUP BY e.id
        ";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    public static function getSharedEvents($userId)
    {
        global $connect;
        $sql = "
        SELECT e.*, u.email AS owner_email
        FROM shared_events s
        JOIN events e ON s.event_id = e.id
        JOIN users u ON e.user_id = u.id  -- owner of the event
        WHERE s.shared_with_user_id = ?
    ";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
