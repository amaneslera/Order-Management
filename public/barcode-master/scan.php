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
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        #interactive.viewport {
            position: relative;
            width: 100%;
            height: 400px;
            overflow: hidden;
            text-align: center;
        }
        #interactive.viewport > canvas, #interactive.viewport > video {
            max-width: 100%;
            width: 100%;
        }
        canvas.drawing, canvas.drawingBuffer {
            position: absolute;
            left: 0;
            top: 0;
        }
        .scanner-overlay {
            display: none;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 10;
        }
        .scanner-badge {
            display: none;
            position: absolute;
            top: 0;
            right: 0;
            background-color: white;
            padding: 5px 10px;
            z-index: 11;
        }
        .result-block {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Barcode Scanner</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../dashboard">← Back to Main Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Scan Barcode</h5>
                    </div>
                    <div class="card-body">
                        <!-- Camera input -->
                        <div id="interactive" class="viewport">
                            <video autoplay="true" preload="auto" src="" muted="true" playsinline="true"></video>
                            <canvas class="drawingBuffer"></canvas>
                        </div>

                        <!-- Result display -->
                        <div class="result-block mt-3" id="result">
                            <div class="alert alert-info">Scanning... Point your camera at a barcode.</div>
                        </div>

                        <!-- Controls -->
                        <div class="controls mt-3">
                            <button class="btn btn-primary" id="startButton">Start Scanning</button>
                            <button class="btn btn-danger" id="stopButton" disabled>Stop Scanning</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <script>
        $(function() {
            // Barcode scanner initialization
            let scannerActive = false;

            function startScanner() {
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector('#interactive'),
                        constraints: {
                            width: 640,
                            height: 480,
                            facingMode: "environment" // Use back camera on mobile
                        },
                    },
                    decoder: {
                        readers: [
                            "code_128_reader",
                            "ean_reader",
                            "ean_8_reader",
                            "code_39_reader",
                            "code_39_vin_reader",
                            "codabar_reader",
                            "upc_reader",
                            "upc_e_reader",
                            "i2of5_reader"
                        ],
                        debug: {
                            showCanvas: true,
                            showPatches: true,
                            showFoundPatches: true,
                            showSkeleton: true,
                            showLabels: true,
                            showPatchLabels: true,
                            showRemainingPatchLabels: true,
                            boxFromPatches: {
                                showTransformed: true,
                                showTransformedBox: true,
                                showBB: true
                            }
                        }
                    },
                }, function(err) {
                    if (err) {
                        console.log(err);
                        $('#result').html('<div class="alert alert-danger">Error initializing camera. Please check camera permissions.</div>');
                        return;
                    }
                    
                    console.log("Quagga initialized successfully");
                    Quagga.start();
                    scannerActive = true;
                    $('#startButton').prop('disabled', true);
                    $('#stopButton').prop('disabled', false);
                });

                // When a barcode is detected
                Quagga.onDetected(function(result) {
                    let code = result.codeResult.code;
                    
                    // Display the result
                    $('#result').html('<div class="alert alert-success">Barcode detected: <strong>' + code + '</strong></div>');
                    
                    // Check if the code exists in the database
                    checkBarcode(code);
                    
                    // Optional: Pause scanning for a moment
                    Quagga.pause();
                    setTimeout(function() {
                        if (scannerActive) {
                            Quagga.start();
                        }
                    }, 3000);
                });
            }

            function stopScanner() {
                Quagga.stop();
                scannerActive = false;
                $('#startButton').prop('disabled', false);
                $('#stopButton').prop('disabled', true);
            }

            // Check if barcode exists in database
            function checkBarcode(code) {
                $.ajax({
                    url: 'check_barcode.php',
                    type: 'POST',
                    data: {barcode: code},
                    success: function(response) {
                        let data = JSON.parse(response);
                        if (data.exists) {
                            $('#result').html('<div class="alert alert-success">' +
                                '<h4>' + data.name + '</h4>' +
                                '<p><strong>Barcode:</strong> ' + data.number + '</p>' +
                                '<p><strong>Batch:</strong> ' + data.batchnumber + '</p>' +
                                '<p><strong>Description:</strong> ' + data.prodesc + '</p>' +
                                '</div>');
                        } else {
                            $('#result').html('<div class="alert alert-warning">Barcode not found in database: <strong>' + code + '</strong></div>');
                        }
                    },
                    error: function() {
                        $('#result').html('<div class="alert alert-danger">Error checking barcode in database.</div>');
                    }
                });
            }

            // Event listeners
            $('#startButton').on('click', function() {
                startScanner();
            });

            $('#stopButton').on('click', function() {
                stopScanner();
            });

            // Clean up when leaving the page
            $(window).on('beforeunload', function() {
                if (scannerActive) {
                    Quagga.stop();
                }
            });
        });
    </script>
</body>
</html>