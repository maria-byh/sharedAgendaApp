<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '', 
    'secure' => isset($_SERVER['HTTPS']), // only on HTTPS
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
        $msg = AuthController::login($_POST["email"], $_POST["password"]);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../../frontend/assets/css/style.css">
    <title>Login</title>
</head>

<body>
    <div class="form">
        <form action="" method="post">
            <h2>Login</h2>
            <p><?= $msg ?></p>
            <!-- CSRF token hidden field -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>"> 
            <div class="form-group">
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="enter your email"
                    class="form-control"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required>
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