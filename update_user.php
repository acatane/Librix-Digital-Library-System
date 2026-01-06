<?php
session_start();
require_once __DIR__ . '/../content/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($password) && $password === $confirm_password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $update = "UPDATE users SET `password` = '$hashedPassword' WHERE id = $user_id";

        if ($conn->query($update) === TRUE) {
            // Redirect back with GET parameter for alert
            header("Location: profile.php?password=changed");
            exit();
        } else {
            header("Location: profile.php?password=error");
            exit();
        }
    } else {
        header("Location: profile.php?password=mismatch");
        exit();
    }
}
?>
