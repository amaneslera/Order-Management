<?php 
include 'session.php';
include 'connect.php';
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
                        <?php
                        // Redirect based on user role
                        $dashboardUrl = '../../admin'; // Default to admin
                        if (isset($_SESSION['role'])) {
                            if ($_SESSION['role'] == 'cashier' || strtolower($_SESSION['role']) == 'cashier') {
                                $dashboardUrl = '../../cashier';
                            } elseif ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'Admin') {
                                $dashboardUrl = '../../admin';
                            }
                        }
                        ?>
                        <a class="nav-link" href="<?= $dashboardUrl ?>">← Back to Dashboard</a>
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

    <div class="container mt-4">
        <h2>Barcode Scanner Dashboard</h2>
        <a href="easy-barcode.php" class="btn btn-info mb-4">
            <i class="fa fa-barcode"></i> Generate Barcodes
        </a>
        <a href="scan.php" class="btn btn-success mb-4">
            <i class="fa fa-camera"></i> Camera Scanner
        </a>

        <!-- Barcode input for physical scanner or manual entry -->
        <div class="card mb-4">
            <div class="card-header">Scan or Enter Barcode</div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" id="barcodeInput" class="form-control" placeholder="Scan or enter barcode here" autofocus>
                    <div class="input-group-append">
                        <button class="btn btn-primary" id="lookupBtn" type="button">Lookup</button>
                    </div>
                </div>
                <div id="productInfo"></div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/addons/datatables.min.js"></script>
    <script>
    $(document).ready(function() {
        // Always focus the input field
        $('#barcodeInput').focus();
        $(document).on('click', function() {
            $('#barcodeInput').focus();
        });
        
        // Lookup product info when barcode is entered
        function lookupBarcode(code) {
            if (code) {
                $('#productInfo').html('<div class="alert alert-info">Searching for barcode: ' + code + '</div>');
                $.ajax({
                    url: 'check_barcode.php',
                    type: 'POST',
                    data: {barcode: code},
                    success: function(response) {
                        let data = {};
                        try { data = JSON.parse(response); } catch(e) {}
                        if (data.exists) {
                            $('#productInfo').html(
                                '<div class="alert alert-success">' +
                                '<h4>' + data.name + '</h4>' +
                                '<p><strong>Barcode:</strong> ' + data.number + '</p>' +
                                '<p><strong>Batch:</strong> ' + data.batchnumber + '</p>' +
                                '<p><strong>Description:</strong> ' + data.prodesc + '</p>' +
                                '</div>'
                            );
                            // Auto-clear input after finding a valid product
                            setTimeout(function() {
                                $('#barcodeInput').val('').focus();
                            }, 500); // Short delay so user can see what was found
                        } else {
                            $('#productInfo').html('<div class="alert alert-warning">Barcode not found in database: <strong>' + code + '</strong></div>');
                        }
                    },
                    error: function() {
                        $('#productInfo').html('<div class="alert alert-danger">Error checking barcode in database.</div>');
                    }
                });
            }
        }

        // Search database on every keyup event (real-time searching)
        let searchTimer;
        $('#barcodeInput').on('input', function() {
            let currentValue = $(this).val().trim();
            
            // Clear any pending search timer
            clearTimeout(searchTimer);
            
            // Set a slight delay to avoid hammering the server with every keystroke
            searchTimer = setTimeout(function() {
                if (currentValue.length > 0) {
                    lookupBarcode(currentValue);
                } else {
                    $('#productInfo').html('');
                }
            }, 200); // 200ms delay is short enough to feel responsive but reduces server load
        });
        
        // Also keep the Enter key functionality for quick submission
        $('#barcodeInput').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                lookupBarcode($(this).val().trim());
                return false; // Prevent form submission
            }
        });
        
        // For manual button click
        $('#lookupBtn').on('click', function() {
            lookupBarcode($('#barcodeInput').val().trim());
        });
    });
    </script>
</body>
</html>