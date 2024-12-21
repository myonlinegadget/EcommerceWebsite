<?php
session_start(); // Start the session

// Destroy all session data
session_unset();
session_destroy();

// Redirect to the admin login page
header("Location: index.html");
exit(); // Ensure no further code is executed
?>
