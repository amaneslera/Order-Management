<?php
session_start();
// Redirect to dashboard if logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    header("location: dashboard.php");
    exit;
} else {
    // Not logged in - redirect to main login
    header("location: ../../index.php");
    exit;
}
?>