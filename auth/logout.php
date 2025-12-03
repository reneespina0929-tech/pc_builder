


<?php
// Start the session
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to homepage with a message
header("Location: ../pages/homepage.php?message=You have been logged out successfully");
exit();
?>