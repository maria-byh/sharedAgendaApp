<?php
$connect = mysqli_connect("localhost", "root", "1234567890", "shared_agenda");
if (!$connect) {
    echo("Connection failed: " . mysqli_connect_error());
}
?>