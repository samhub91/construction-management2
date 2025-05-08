<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $email = $_SESSION['reset_email'] ?? null;

    if (!$email) {
        echo "Session expired. Please try again.";
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo "Passwords do not match. Please try again.";
        exit;
    }

    if (strlen($newPassword) < 6) {
        echo "Password should be at least 6 characters long.";
        exit;
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update in DB
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hashedPassword, $email]);

    // Clear reset session variable
    unset($_SESSION['reset_email']);

    echo "Password updated successfully. You can now <a href='../login.html'>login</a>.";
}
?>
