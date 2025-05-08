<?php
require_once 'db.php'; // Ensure you have a working database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        echo "All fields are required!";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format!";
        exit();
    }

    // Insert message into the database
    $sql = "INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);
        echo "Message sent successfully!";
        header("Location: ../contact.html?success=true"); // Redirect back to contact page with success
        exit();
    } catch (PDOException $e) {
        echo "Failed to send message: " . $e->getMessage();
    }
}
?>
