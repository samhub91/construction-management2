<?php
require_once 'db.php'; // Ensure this is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the email already exists
    $checkEmailSql = "SELECT COUNT(*) FROM users WHERE email = :email";
    $checkStmt = $conn->prepare($checkEmailSql);
    $checkStmt->execute(['email' => $email]);
    $emailExists = $checkStmt->fetchColumn();

    if ($emailExists) {
        echo "Error: Email already registered. Please use a different email.";
    } else {
        // Insert the new user
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);

            // Start session and set session variables including user ID
            session_start();
            $new_user_id = $conn->lastInsertId(); // ✅ Get newly created user ID
            $_SESSION['user'] = [
                'id' => $new_user_id,   // ✅ Ensure 'id' is set
                'name' => $name,
                'email' => $email,
                'role' => 'user'        // Optional: default role if needed
            ];

            header("Location: ../home.php"); // Redirect to home page
            exit();
        } catch (PDOException $e) {
            echo "Registration failed: " . $e->getMessage();
        }
    }
}
?>
