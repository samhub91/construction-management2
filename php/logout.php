<?php
session_start();

// Destroy session data
session_unset();
session_destroy();

// Redirect to the login page after logging out
header("Location: ../login.html");
exit();
?>