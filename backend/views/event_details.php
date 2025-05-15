<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/db.php';

$userId = $_SESSION['id'];
$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if ($eventId <= 0) {
    die("Invalid event ID.");
}

// Check if the event belongs to the user or is shared with the user
$sql = "
    SELECT e.*, u.email AS owner_email
    FROM events e
    JOIN users u ON e.user_id = u.id
    WHERE e.id = ? AND (
        e.user_id = ? OR 
        EXISTS (
            SELECT 1 FROM shared_events s WHERE s.event_id = e.id AND s.shared_with_user_id = ?
        )
    )
    LIMIT 1
";

$stmt = $connect->prepare($sql);
$stmt->bind_param("iii", $eventId, $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("You do not have permission to view this event or event does not exist.");
}

$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../../frontend/assets/css/event-details.css">
    <title>Event Details</title>
</head>

<body>
    <header>
        <div class="header-bar">
            <div class="toolbar">
                <a href="dashboard.php" class="btn-back">← Back to Calendar</a>
            </div>
            <div class="toolbar">
                <a href="dashboard.php?view=table">📋 Table View</a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </div>
    </header>
    <main>
        <h2><?= htmlspecialchars($event['title']) ?></h2>
        <section class="agenda">
            <div class="event">
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
                <p><strong>When:</strong> <?= date('Y-m-d H:i', strtotime($event['start_time'])) ?>
                    to <?= date('Y-m-d H:i', strtotime($event['end_time'])) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($event['location'] ?? 'N/A') ?></p>
                <p><strong>Owner:</strong> <?= htmlspecialchars($event['owner_email']) ?></p>

                <?php if ($event['user_id'] === $userId): ?>
                    <a href="edit_event.php?event_id=<?= $event['id'] ?>">✏️ Edit</a>
                <?php endif; ?>
                <?php if ($event['user_id'] == $_SESSION['id']): ?>
                    <a href="../controllers/delete_event.php?event_id=<?= $event['id'] ?>" onclick="return confirm('are you sure you want to delete the event?');">🗑️ Delete</a>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>

</html>