<?php
session_start();
include_once "config.php";

// Set session from GET (from iframe)
if (isset($_GET['user'])) {
    $_SESSION['username'] = $_GET['user'];
    // Fetch user info from your main users table
    $username = mysqli_real_escape_string($conn, $_SESSION['username']);
    $sql = "SELECT * FROM users WHERE username = '{$username}' LIMIT 1";
    $query = mysqli_query($conn, $sql);
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
    } else {
        die("User not found.");
    }
}

// Only allow access if user_id is set
if(!isset($_SESSION['user_id'])){
    echo "<h2>Access denied. Please log in through the main application.</h2>";
    exit;
}

// Now, get all other users except the current one
$outgoing_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id != {$outgoing_id} ORDER BY id DESC";
$query = mysqli_query($conn, $sql);
$output = "";
if(mysqli_num_rows($query) == 0){
    $output .= "No users are currently online";
}elseif(mysqli_num_rows($query) > 0){
    include_once "data.php";
}
echo $output;
?>