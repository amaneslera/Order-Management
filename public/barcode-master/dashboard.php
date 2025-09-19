<?php 
include 'session.php';
include 'connect.php'; // Using the fixed connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner</title>
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <link href="css/addons/datatables.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Add a navigation bar with link back to main system -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Barcode Scanner</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../../dashboard">← Back to Dashboard</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <span class="nav-link">Logged in as: <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)</span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Rest of your dashboard content -->
    <div class="container mt-4">
        <h2>Barcode Scanner Dashboard</h2>
        
        <!-- Your existing dashboard content goes here -->
        <a href="easy-barcode.php" class="btn btn-info mb-4">
            <i class="fa fa-barcode"></i> Generate Barcodes
        </a>
        
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/addons/datatables.min.js"></script>
</body>
</html>