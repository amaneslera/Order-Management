<?php
// Connect to your main database (employee_db)
$connection = mysqli_connect('localhost', 'root', '', 'employee_db');
if (!$connection){
    die("Connection failed: " . mysqli_connect_error());
}
?>