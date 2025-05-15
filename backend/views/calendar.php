<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>My Events Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }

        .fc-event.created {
            background-color: #3788d8 !important;
            border-color: #2c6ebd !important;
        }

        .fc-event.shared {
            background-color: #34a853 !important;
            border-color: #2a8748 !important;
        }
    </style>
</head>

<body>
    <h1>📅 My Events Calendar</h1>
    <div id="calendar"></div>

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
                events: '../controllers/events_api.php', // URL to fetch events JSON
                eventClassNames: function(arg) {
                    // Add custom class based on eventType for styling
                    if (arg.event.extendedProps.eventType) {
                        return [arg.event.extendedProps.eventType];
                    }
                    return [];
                },
                eventClick: function(info) {
                    // Redirect to event details page on click
                    const eventId = info.event.id;
                    window.location.href = `event_details.php?event_id=${eventId}`;
                },
                eventDidMount: function(info) {
                    // Add tooltip with description if exists
                    if (info.event.extendedProps.description) {
                        info.el.setAttribute('title', info.event.extendedProps.description);
                    }
                }
            });
            calendar.render();
        });
    </script>
</body>

</html>