<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . '/../models/Event.php';
$userId = $_SESSION['id'];

// Fetch user's own events & shared ones
$events = Event::getUserEvents($userId);
$sharedEvents = Event::getSharedEvents($userId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../../frontend/assets/css/style.css">
    <title>Dashboard</title>
</head>

<body>
    <header>
        <h1>Welcome, <?= $_SESSION['user'] ?></h1>
        <nav>
            <a href="add_event.php">➕ Create Event</a>
            <a href="notifications.php">🔔 Notifications</a>
            <a href="calendar.php">🗓️ View Calendar</a>
            <a href="logout.php">🚪 Logout</a>
        </nav>
    </header>

    <main>
        <h2>📅 My Agenda</h2>
        <section class="agenda">
            <?php foreach ($events as $event): ?>
                <div class="event">
                    <h3><?= htmlspecialchars($event['title']) ?></h3>
                    <p><?= htmlspecialchars($event['description']) ?></p>
                    <p><strong>When:</strong>
                        From <?= htmlspecialchars(date('Y-m-d H:i', strtotime($event['start_time']))) ?>
                        <?php if (!empty($event['end_time'])): ?>
                            to <?= htmlspecialchars(date('Y-m-d H:i', strtotime($event['end_time']))) ?>
                        <?php endif; ?>
                    </p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($event['location'] ?? 'N/A') ?></p>
                    <p><strong>Shared With:</strong> <?= !empty($event['shared_with']) ? htmlspecialchars($event['shared_with']) : 'N/A' ?></p>

                    <a href="event_details.php?event_id=<?= $event['id'] ?>">👁️ View</a>
                    <a href="edit_event.php?event_id=<?= $event['id'] ?>">✏️ Edit</a>
                </div>
            <?php endforeach; ?>
        </section>

        <h2>📤 Shared With Me</h2>
        <section class="shared-agenda">
            <?php foreach ($sharedEvents as $event): ?>
                <div class="event">
                    <h3><?= htmlspecialchars($event['title']) ?></h3>
                    <p><?= htmlspecialchars($event['description']) ?></p>
                    <p><strong>When:</strong>
                        From <?= htmlspecialchars(date('Y-m-d H:i', strtotime($event['start_time']))) ?>
                        <?php if (!empty($event['end_time'])): ?>
                            to <?= htmlspecialchars(date('Y-m-d H:i', strtotime($event['end_time']))) ?>
                        <?php endif; ?>
                    </p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($event['location'] ?? 'N/A') ?></p>
                    <p><strong>Shared By:</strong> <?= htmlspecialchars($event['owner_email'] ?? 'N/A') ?></p>
                    <a href="event_details.php?event_id=<?= $event['id'] ?>">👁️ View</a>
                    <!-- No edit option for shared events -->
                </div>
            <?php endforeach; ?>
        </section>
    </main>
</body>

</html>