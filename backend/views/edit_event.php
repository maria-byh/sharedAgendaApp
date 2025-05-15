<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$msg = "";
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if ($event_id === 0) {
    $msg = "Event not found.";
} else {
    // Retrieve the event details from the database
    $stmt = $connect->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $event = $result->fetch_assoc();
        // Get shared email if exists
        $stmtShared = $connect->prepare("SELECT u.email FROM shared_events s JOIN users u ON s.shared_with_user_id = u.id WHERE s.event_id = ?");
        $stmtShared->bind_param("i", $event_id);
        $stmtShared->execute();
        $resultShared = $stmtShared->get_result();
        $sharedWithEmail = $resultShared->num_rows > 0 ? $resultShared->fetch_assoc()['email'] : "";

        // Check if the logged-in user is the creator of the event
        if ($event['user_id'] !== $_SESSION['id']) {
            $msg = "You are not allowed to edit this event.";
        } else {
            // Check if the form has been submitted
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $title = htmlspecialchars(trim($_POST['title']));
                $description = htmlspecialchars(trim($_POST['description']));
                $start_time = htmlspecialchars(trim($_POST['start_time']));
                $end_time = htmlspecialchars(trim($_POST['end_time']));
                $location = htmlspecialchars(trim($_POST['location']));
                $shared_with = htmlspecialchars(trim($_POST['shared_with']));

                // Validate the inputs
                if (empty($title) || empty($description) || empty($start_time) || empty($end_time) || empty($location)) {
                    $msg = "Please fill in all fields.";
                } elseif (strtotime($end_time) <= strtotime($start_time)) {
                    $msg = "End time must be after start time.";
                } else {
                    // Update event
                    $stmt_update = $connect->prepare("UPDATE events SET title = ?, description = ?, start_time = ?, end_time = ?, location = ? WHERE id = ?");
                    $stmt_update->bind_param("sssssi", $title, $description, $start_time, $end_time, $location, $event_id);
                    $stmt_update->execute();

                    if ($stmt_update->affected_rows >= 0) {
                        // Remove previous shared entry if exists
                        $stmtDelShare = $connect->prepare("DELETE FROM shared_events WHERE event_id = ?");
                        $stmtDelShare->bind_param("i", $event_id);
                        $stmtDelShare->execute();

                        // Add new shared email if provided
                        if (!empty($shared_with)) {
                            $stmtUser = $connect->prepare("SELECT id FROM users WHERE email = ?");
                            $stmtUser->bind_param("s", $shared_with);
                            $stmtUser->execute();
                            $resultUser = $stmtUser->get_result();

                            if ($resultUser->num_rows > 0) {
                                $sharedUserId = $resultUser->fetch_assoc()['id'];
                                $stmtShare = $connect->prepare("INSERT INTO shared_events (event_id, shared_with_user_id) VALUES (?, ?)");
                                $stmtShare->bind_param("ii", $event_id, $sharedUserId);
                                $stmtShare->execute();
                            }
                        }

                        $msg = "Event updated successfully.";
                        // Optional redirect:
                        // header("Location: events.php");
                        // exit();
                    } else {
                        $msg = "Failed to update the event.";
                    }
                }
            }
        }
    } else {
        $msg = "Event not found.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="../../frontend/assets/css/style.css">
</head>

<body>
    <div class="form">
        <form action="" method="post">
            <h2>Edit Event</h2>
            <p class="msg"><?= $msg ?></p>

            <!-- Event Title -->
            <div class="form-group">
                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($event['title'] ?? '') ?>" required>
            </div>

            <!-- Event Description -->
            <div class="form-group">
                <textarea name="description" id="description" class="form-control" required><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
            </div>

            <!-- Event Start Time -->
            <div class="form-group">
                <input type="datetime-local" name="start_time" id="start_time" class="form-control"
                    value="<?= isset($event['start_time']) ? date('Y-m-d\TH:i', strtotime($event['start_time'])) : '' ?>" required>

            </div>

            <!-- Event End Time -->
            <div class="form-group">
                <input type="datetime-local" name="end_time" id="end_time" class="form-control"
                    value="<?= isset($event['end_time']) ? date('Y-m-d\TH:i', strtotime($event['end_time'])) : '' ?>" required>

            </div>

            <!-- Event Location -->
            <div class="form-group">
                <input type="text" name="location" id="location" class="form-control" value="<?= htmlspecialchars($event['location'] ?? '') ?>" required>
            </div>

            <!-- Shared With -->
            <div class="form-group">
                <input type="email" name="shared_with" id="shared_with" class="form-control" placeholder="Share with (email)" value="<?= htmlspecialchars($sharedWithEmail) ?>">
            </div>

            <button type="submit" name="submit" class="btn">Update Event</button>
        </form>
    </div>
</body>

</html>