<?php
include 'session.php';
include 'connect.php';

// Default values
$barcodeText = isset($_POST['barcode_text']) ? $_POST['barcode_text'] : '101010';

// Handle form submission to add a new product
if(isset($_POST['add_product'])) {
    $number = mysqli_real_escape_string($connection, $_POST['barcode_text']);
    $name = mysqli_real_escape_string($connection, $_POST['product_name']);
    $batch = mysqli_real_escape_string($connection, $_POST['batch_number']);
    $desc = mysqli_real_escape_string($connection, $_POST['product_desc']);
    
    // Check if barcode already exists
    $check = mysqli_query($connection, "SELECT * FROM barcode WHERE number='$number'");
    if(mysqli_num_rows($check) > 0) {
        $message = '<div class="alert alert-warning">Barcode already exists in the database.</div>';
    } else {
        $sql = "INSERT INTO barcode (number, name, batchnumber, prodesc) VALUES ('$number', '$name', '$batch', '$desc')";
        if(mysqli_query($connection, $sql)) {
            $message = '<div class="alert alert-success">Product added successfully with barcode: '.$number.'</div>';
        } else {
            $message = '<div class="alert alert-danger">Error adding product: '.mysqli_error($connection).'</div>';
        }
    }
}

// Get existing products for dropdown
$products = mysqli_query($connection, "SELECT * FROM barcode ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Generator</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .barcode-container {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
        }
        .barcode-text {
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }
        @media print {
            .no-print { display: none; }
            .barcode-container { border: none; }
        }
    </style>
    <!-- Include JsBarcode library directly from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Barcode Generator</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="scan.php">Scan Barcode</a>
                    </li>
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
                        <a class="nav-link" href="<?= $dashboardUrl ?>">← Back to Main Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if(isset($message)) echo $message; ?>
        
        <div class="row">
            <!-- Existing Products -->
            <div class="col-md-4">
                <div class="card no-print">
                    <div class="card-header">
                        <h5>Existing Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="existingProduct">Select a product:</label>
                            <select class="form-control" id="existingProduct">
                                <option value="">-- Select a product --</option>
                                <?php while($row = mysqli_fetch_assoc($products)): ?>
                                <option value="<?php echo $row['number']; ?>" 
                                        data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                        data-batch="<?php echo htmlspecialchars($row['batchnumber']); ?>"
                                        data-desc="<?php echo htmlspecialchars($row['prodesc']); ?>">
                                    <?php echo htmlspecialchars($row['name']); ?> (<?php echo $row['number']; ?>)
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barcode Generator Form -->
            <div class="col-md-8">
                <div class="card no-print">
                    <div class="card-header">
                        <h5>Generate Barcode</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" id="barcodeForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="barcode_text">Barcode Text/Number:</label>
                                        <input type="text" class="form-control" id="barcode_text" name="barcode_text" 
                                               value="<?php echo htmlspecialchars($barcodeText); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="barcode_type">Barcode Type:</label>
                                        <select class="form-control" id="barcode_type">
                                            <option value="code128">Code 128</option>
                                            <option value="code39">Code 39</option>
                                            <option value="ean13">EAN-13</option>
                                            <option value="ean8">EAN-8</option>
                                            <option value="upc">UPC</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="generateBarcode" class="btn btn-primary">Generate Barcode</button>
                            
                            <!-- Add New Product Section -->
                            <hr>
                            <h5>Add as New Product</h5>
                            <div class="form-group">
                                <label for="product_name">Product Name:</label>
                                <input type="text" class="form-control" id="product_name" name="product_name">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="batch_number">Batch Number:</label>
                                        <input type="text" class="form-control" id="batch_number" name="batch_number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_desc">Product Description:</label>
                                        <input type="text" class="form-control" id="product_desc" name="product_desc">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="add_product" class="btn btn-success">Add Product with Barcode</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Barcode Display and Print Area -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Generated Barcode</h5>
                        <button id="printBarcode" class="btn btn-sm btn-secondary no-print">Print Barcode</button>
                    </div>
                    <div class="card-body">
                        <div class="barcode-container">
                            <svg id="barcodeCanvas"></svg>
                            <div class="barcode-text" id="barcodeText"><?php echo htmlspecialchars($barcodeText); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Generate initial barcode
            generateBarcode();
            
            // Generate barcode on button click
            $('#generateBarcode').click(function() {
                generateBarcode();
            });
            
            // Generate barcode function
            function generateBarcode() {
                const barcodeValue = $('#barcode_text').val();
                const barcodeType = $('#barcode_type').val();
                
                JsBarcode("#barcodeCanvas", barcodeValue, {
                    format: barcodeType,
                    width: 2,
                    height: 60,
                    displayValue: false
                });
                
                $('#barcodeText').text(barcodeValue);
            }
            
            // Handle existing product selection
            $('#existingProduct').change(function() {
                var selectedOption = $(this).find('option:selected');
                if (selectedOption.val()) {
                    $('#barcode_text').val(selectedOption.val());
                    $('#product_name').val(selectedOption.data('name'));
                    $('#batch_number').val(selectedOption.data('batch'));
                    $('#product_desc').val(selectedOption.data('desc'));
                    
                    // Generate barcode for selected product
                    generateBarcode();
                }
            });
            
            // Print functionality
            $('#printBarcode').click(function() {
                window.print();
            });
        });
    </script>
</body>
</html>