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
    <link rel="stylesheet" href="../../frontend/assets/css/style.css">
</head>

<body>
    <div class="form">
        <form action="" method="post">
            <h2>Create New Event</h2>
            <p><?= $msg ?></p>

            <input type="text" name="title" placeholder="Title" required><br>
            <textarea name="description" placeholder="Description"></textarea><br>
            <input type="datetime-local" name="datetime" required><br>

            <!-- New field for end time -->
            <input type="datetime-local" name="end_datetime" required><br>
            <input type="text" name="location" placeholder="Location" required><br>
            <input type="email" name="shared_with" placeholder="Share with (optional email)"><br>

            <button type="submit">Add Event</button>
        </form>

    </div>
</body>

</html>