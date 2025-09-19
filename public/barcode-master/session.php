<?php
// Replace the entire content of session.php with this
session_start();

// Check if user is logged in from main application
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    // Not logged in - redirect to main login
    header("location: ../../index.php");
    exit;
}

// Set barcode system variables based on main system session
$_SESSION['c_user'] = $_SESSION['username'];
$_SESSION['user_role'] = $_SESSION['role'];

// Ensure only admin/cashier can access
if ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'cashier') {
    header("location: ../../index.php");
    exit;
}
?>