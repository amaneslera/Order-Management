<?php
include 'session.php';
include 'connect.php';

// Include all required barcode files manually
require_once '../php-barcode-generator-main/src/Exceptions/BarcodeException.php';
require_once '../php-barcode-generator-main/src/Exceptions/InvalidCharacterException.php';
require_once '../php-barcode-generator-main/src/Exceptions/InvalidCheckDigitException.php';
require_once '../php-barcode-generator-main/src/Exceptions/InvalidFormatException.php';
require_once '../php-barcode-generator-main/src/Exceptions/InvalidLengthException.php';
require_once '../php-barcode-generator-main/src/Exceptions/InvalidOptionException.php';
require_once '../php-barcode-generator-main/src/Exceptions/UnknownColorException.php';
require_once '../php-barcode-generator-main/src/Exceptions/UnknownTypeException.php';

require_once '../php-barcode-generator-main/src/Helpers/BinarySequenceConverter.php';
require_once '../php-barcode-generator-main/src/Helpers/ColorHelper.php';
require_once '../php-barcode-generator-main/src/Helpers/StringHelpers.php';

require_once '../php-barcode-generator-main/src/Types/TypeInterface.php';
require_once '../php-barcode-generator-main/src/Types/TypeEanUpcBase.php';
require_once '../php-barcode-generator-main/src/Types/TypeCode128.php';
require_once '../php-barcode-generator-main/src/Types/TypeCode39.php';
require_once '../php-barcode-generator-main/src/Types/TypeEan13.php';
require_once '../php-barcode-generator-main/src/Types/TypeEan8.php';
require_once '../php-barcode-generator-main/src/Types/TypeUpcA.php';
require_once '../php-barcode-generator-main/src/Types/TypeUpcE.php';

require_once '../php-barcode-generator-main/src/BarcodeGenerator.php';
require_once '../php-barcode-generator-main/src/BarcodeGeneratorPNG.php';
require_once '../php-barcode-generator-main/src/BarcodeGeneratorSVG.php';
require_once '../php-barcode-generator-main/src/BarcodeGeneratorJPG.php';
require_once '../php-barcode-generator-main/src/BarcodeGeneratorHTML.php';

use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorJPG;
use Picqer\Barcode\BarcodeGeneratorHTML;

// Default values
$barcodeText = isset($_POST['barcode_text']) ? $_POST['barcode_text'] : '101010';
$barcodeType = isset($_POST['barcode_type']) ? $_POST['barcode_type'] : 'code128';
$barcodeWidth = isset($_POST['barcode_width']) ? (int)$_POST['barcode_width'] : 2;
$barcodeHeight = isset($_POST['barcode_height']) ? (int)$_POST['barcode_height'] : 60;
$format = isset($_POST['format']) ? $_POST['format'] : 'png';

// Generate barcode
function generateBarcode($text, $type, $width, $height, $format) {
    switch ($format) {
        case 'png':
            $generator = new BarcodeGeneratorPNG();
            $barcode = '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($text, $type, $width, $height)) . '">';
            break;
        case 'svg':
            $generator = new BarcodeGeneratorSVG();
            $barcode = $generator->getBarcode($text, $type, $width, $height);
            break;
        case 'jpg':
            $generator = new BarcodeGeneratorJPG();
            $barcode = '<img src="data:image/jpeg;base64,' . base64_encode($generator->getBarcode($text, $type, $width, $height)) . '">';
            break;
        case 'html':
            $generator = new BarcodeGeneratorHTML();
            $barcode = $generator->getBarcode($text, $type, $width, $height);
            break;
        default:
            $generator = new BarcodeGeneratorPNG();
            $barcode = '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($text, $type, $width, $height)) . '">';
    }
    return $barcode;
}

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
        .print-section {
            display: none;
        }
        @media print {
            .no-print { display: none; }
            .print-section { display: block; }
            .barcode-container { border: none; }
        }
    </style>
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
                                        <input type="text" class="form-control" id="barcode_text" name="barcode_text" value="<?php echo htmlspecialchars($barcodeText); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="barcode_type">Barcode Type:</label>
                                        <select class="form-control" id="barcode_type" name="barcode_type">
                                            <option value="code128" <?php if($barcodeType == 'code128') echo 'selected'; ?>>Code 128</option>
                                            <option value="code39" <?php if($barcodeType == 'code39') echo 'selected'; ?>>Code 39</option>
                                            <option value="ean13" <?php if($barcodeType == 'ean13') echo 'selected'; ?>>EAN-13</option>
                                            <option value="ean8" <?php if($barcodeType == 'ean8') echo 'selected'; ?>>EAN-8</option>
                                            <option value="upca" <?php if($barcodeType == 'upca') echo 'selected'; ?>>UPC-A</option>
                                            <option value="upce" <?php if($barcodeType == 'upce') echo 'selected'; ?>>UPC-E</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="barcode_width">Bar Width:</label>
                                        <input type="number" class="form-control" id="barcode_width" name="barcode_width" value="<?php echo $barcodeWidth; ?>" min="1" max="4">
                                    </div>
                                    <div class="form-group">
                                        <label for="barcode_height">Bar Height:</label>
                                        <input type="number" class="form-control" id="barcode_height" name="barcode_height" value="<?php echo $barcodeHeight; ?>" min="20" max="120">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Format:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="format" id="format_png" value="png" <?php if($format == 'png') echo 'checked'; ?>>
                                    <label class="form-check-label" for="format_png">PNG</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="format" id="format_svg" value="svg" <?php if($format == 'svg') echo 'checked'; ?>>
                                    <label class="form-check-label" for="format_svg">SVG</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="format" id="format_jpg" value="jpg" <?php if($format == 'jpg') echo 'checked'; ?>>
                                    <label class="form-check-label" for="format_jpg">JPG</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="format" id="format_html" value="html" <?php if($format == 'html') echo 'checked'; ?>>
                                    <label class="form-check-label" for="format_html">HTML</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Generate Barcode</button>
                            
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
                            <div id="barcodeResult">
                                <?php echo generateBarcode($barcodeText, $barcodeType, $barcodeWidth, $barcodeHeight, $format); ?>
                            </div>
                            <div class="barcode-text"><?php echo htmlspecialchars($barcodeText); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Print Section -->
        <div class="print-section">
            <div class="barcode-container">
                <?php echo generateBarcode($barcodeText, $barcodeType, $barcodeWidth, $barcodeHeight, $format); ?>
                <div class="barcode-text"><?php echo htmlspecialchars($barcodeText); ?></div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle existing product selection
            $('#existingProduct').change(function() {
                var selectedOption = $(this).find('option:selected');
                if (selectedOption.val()) {
                    $('#barcode_text').val(selectedOption.val());
                    $('#product_name').val(selectedOption.data('name'));
                    $('#batch_number').val(selectedOption.data('batch'));
                    $('#product_desc').val(selectedOption.data('desc'));
                    
                    // Auto-submit the form to generate the barcode
                    $('#barcodeForm').submit();
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