<?php
include("connection.php");

$msg = "";
if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];

    if ($password !== $cpassword) {
        $msg = "Password and Confirm Password do not match.";
    } else {
        $select = "SELECT * FROM users WHERE email = ?";
        $stmt = $connect->prepare($select);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $msg = "User already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = $connect->prepare($insert);
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            $stmt->execute();
            header("Location: login.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../frontend/assets/css/style.css">
    <title>Document</title>
</head>

<body>
    <div class="form">
        <form action="" method="post">
            <h2>Registration</h2>
            <p class="msg"><?= $msg ?></p>
            <div class="form-group">
                <input type="text" name="name" id="name" placeholder="enter your name" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="enter your email" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" placeholder="enter your password" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" name="cpassword" id="cpassword" placeholder="confirm your password" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn">Register</button>

            <p>Already have an account? <a href="login.php">Login now</a></p>


        </form>
    </div>
</body>

</html>