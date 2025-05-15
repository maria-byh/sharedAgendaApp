<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . '/../models/Event.php';
$userId = $_SESSION['id'];

// Fetch events
$events = Event::getUserEvents($userId);
$sharedEvents = Event::getSharedEvents($userId);

// Determine view
$view = $_GET['view'] ?? 'calendar'; // 'calendar' or 'table'
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Agenda</title>
    <link rel="stylesheet" href="../../frontend/assets/css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

</head>

<body>
    <header>
        <div class="header-bar">
            <div class="user-name">👤  Welcome, <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?>!</div>
            <div class="toolbar">
                <a href="add_event.php">➕ Create Event</a>
                <?php if ($view !== 'table'): ?>
                    <a href="dashboard.php?view=table">📋 Table View</a>
                <?php endif; ?>
                <?php if ($view !== 'calendar'): ?>
                    <a href="dashboard.php?view=calendar">🗓️ Calendar View</a>
                <?php endif; ?>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </div>

    </header>

    <main>
        <?php if ($view === 'calendar'): ?>
            <div id="calendar"></div>
        <?php else: ?>
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
                        <?php if ($event['user_id'] == $_SESSION['id']): ?>
                            <a href="../controllers/delete_event.php?event_id=<?= $event['id'] ?>" onclick="return confirm('are you sure you want to delete the event?');">🗑️ Delete</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>

            <h2>📤 Events Shared With Me</h2>
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
                    </div>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <?php if ($view === 'calendar'): ?>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: '../controllers/events_api.php',
                    eventClassNames: function(arg) {
                        if (arg.event.extendedProps.eventType) {
                            return [arg.event.extendedProps.eventType];
                        }
                        return [];
                    },
                    eventClick: function(info) {
                        const eventId = info.event.id;
                        window.location.href = `event_details.php?event_id=${eventId}`;
                    },
                    eventDidMount: function(info) {
                        if (info.event.extendedProps.description) {
                            info.el.setAttribute('title', info.event.extendedProps.description);
                        }
                    }
                });
                calendar.render();
            });
        </script>
    <?php endif; ?>
</body>

</html>