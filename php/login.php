<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];

        // Query to fetch the user from the database
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Store user details in the session, including role
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'] // Make sure 'role' is included here
            ];

            // Redirect to home or the admin page if the user is an admin
            if ($user['role'] == 'admin') {
                header("Location: admin/admin.php"); // No need for '..' since both are in the same folder

            } else {
                header("Location: ../home.php"); // Redirect to normal home page
            }
            exit();
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Please enter both email and password.";
    }
}
?>
