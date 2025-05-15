<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../models/User.php'; // for fetching user list if needed
require_once __DIR__ . '/../controllers/EventController.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $msg = EventController::addEvent(
        $_POST["title"],
        $_POST["description"],
        $_POST["datetime"],
        $_POST["end_datetime"],      // add this
        $_POST["location"],
        $_POST["shared_with"] ?? null // optional, provide default null if empty
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Event</title>
    <link rel="stylesheet" href="../../frontend/assets/css/event-form.css">
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
        <div class="form-container">

            <h2>Create New Event</h2>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
            <form action="" method="post" novalidate>
                <input type="text" name="title" placeholder="Title" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"><br>

                <textarea name="description" placeholder="Description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea><br>

                <input type="datetime-local" name="datetime" required value="<?= htmlspecialchars($_POST['datetime'] ?? '') ?>"><br>

                <input type="datetime-local" name="end_datetime" required value="<?= htmlspecialchars($_POST['end_datetime'] ?? '') ?>"><br>

                <input type="text" name="location" placeholder="Location" required value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"><br>

                <input type="email" name="shared_with" placeholder="Share with (optional email)" value="<?= htmlspecialchars($_POST['shared_with'] ?? '') ?>"><br>

                <button type="submit">Add Event</button>
            </form>

        </div>
    </main>
</body>

</html>