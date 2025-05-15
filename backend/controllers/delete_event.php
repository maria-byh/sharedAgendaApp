<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['event_id'])) {
    header("Location: ../views/dashboard.php?view=table");
    exit();
}

$eventId = intval($_GET['event_id']);
$userId = $_SESSION['id'];

// تحقق من ملكية الحدث قبل الحذف
$stmt = $connect->prepare("SELECT user_id FROM events WHERE id = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // الحدث غير موجود
    header("Location: ../views/dashboard.php?view=table");
    exit();
}

$event = $result->fetch_assoc();

if ($event['user_id'] !== $userId) {
    // المستخدم ليس صاحب الحدث
    header("Location: ../views/dashboard.php?view=table");
    exit();
}

// حذف الحدث
$deleteStmt = $connect->prepare("DELETE FROM events WHERE id = ?");
$deleteStmt->bind_param("i", $eventId);
$deleteStmt->execute();

// حذف مشاركات الحدث من shared_events أيضًا
$deleteSharedStmt = $connect->prepare("DELETE FROM shared_events WHERE event_id = ?");
$deleteSharedStmt->bind_param("i", $eventId);
$deleteSharedStmt->execute();

header("Location: ../views/dashboard.php?view=table");
exit();
