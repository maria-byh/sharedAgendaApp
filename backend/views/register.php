<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a CSRF token
}

require_once __DIR__ . '/../controllers/AuthController.php';
$msg = "";

// Check if the user is already logged in
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $msg = "Invalid CSRF token.";
    } else {
        $msg = AuthController::register($_POST["name"], $_POST["email"], $_POST["password"], $_POST["cpassword"]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../../frontend/assets/css/style.css">
    <title>Document</title>
</head>

<body>
    <div class="login-container">
        <div class="form-side">
            <form action="" method="post">
                <h2>Registration</h2>
                <p class="msg"><?= $msg ?></p>
                <!-- CSRF token -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="form-group">
                    <input type="text" name="name" id="name" placeholder="enter your name" class="form-control"
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                        required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" id="email" placeholder="enter your email" class="form-control"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required>
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
        <div class="image-side">
            <img src="../../frontend/assets/images/login-illustration.png" alt="Login Illustration">
        </div>
    </div>
</body>

</html>