<?php
include 'connection.php';

if(isset($_POST['barcode'])) {
    $barcode = mysqli_real_escape_string($con, $_POST['barcode']);
    
    // Query the database for the barcode
    $query = "SELECT * FROM barcode WHERE number = '$barcode' LIMIT 1";
    $result = mysqli_query($con, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode([
            'exists' => true,
            'name' => $row['name'],
            'number' => $row['number'],
            'batchnumber' => $row['batchnumber'],
            'prodesc' => $row['prodesc']
        ]);
    } else {
        echo json_encode([
            'exists' => false
        ]);
    }
} else {
    echo json_encode([
        'error' => 'No barcode provided'
    ]);
}
?>