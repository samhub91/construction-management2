<?php
require_once 'db.php'; // Make sure the path is correct

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $_SESSION['reset_email'] = $email;

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        header("Location: ../reset_password.html");
        exit();
    } else {
        echo "Email not found in our records.";
    }
}
?>
