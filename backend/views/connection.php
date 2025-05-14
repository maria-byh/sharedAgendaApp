<?php
$connect = mysqli_connect("localhost", "root", "", "shared_agenda");
if (!$connect) {
    echo("Connection failed: " . mysqli_connect_error());
}
?>