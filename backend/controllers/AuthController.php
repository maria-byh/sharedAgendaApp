<?php
require_once __DIR__ . '/../config/db.php';

class AuthController
{
    public static function login($email, $password)
    {
        global $connect;

        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL); // Validate email format
        if (!$email) {
            return "Invalid email address.";
        }

        $stmt = $connect->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                session_start(); 
                session_regenerate_id(true); // Regenerate session ID to prevent session fixation
                $_SESSION['user'] = $user['email'];
                $_SESSION['id'] = $user['id'];
                header("Location: ../views/dashboard.php");
                exit();
            } else {
                return "Invalid credentials.";
            }
        } else {
            return "User not found.";
        }
    }

    public static function register($name, $email, $password, $cpassword)
    {
        global $connect;

        $name = htmlspecialchars(trim($name)); // Sanitize name input
        if (empty($name)) {
            return "Name is required.";
        }

        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL); // Validate email format
        if (!$email) {
            return "Invalid email address.";
        }

        if ($password !== $cpassword) {
            return "Password and Confirm Password do not match.";
        }

        $stmt = $connect->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return "User already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $connect->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $name, $email, $hashed_password);
            $insert->execute();
            header("Location: login.php");
            exit();
        }
    }
}
