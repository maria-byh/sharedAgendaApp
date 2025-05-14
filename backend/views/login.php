<?php
include("connection.php");

$msg = "";
if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $select = "SELECT * FROM users WHERE email = ?";
    $stmt = $connect->prepare($select);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user'] = $user['email'];
            $_SESSION['id'] = $user['id'];
            header("Location: ../../frontend/index.html");
        } else {
            $msg = "Invalid credentials.";
        }
    } else {
        $msg = "User not found.";
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
            <h2>Login</h2>
            <p><?= $msg?></p>
            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="enter your email" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" placeholder="enter your password" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn">Login</button>
            <p>Don't have an account? <a href="register.php">Register now</a></p>
        </form>
    </div>
</body>

</html>