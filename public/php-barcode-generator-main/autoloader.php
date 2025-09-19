<?php
// Define the autoloader function for Picqer namespace
spl_autoload_register(function ($class) {
    // Only handle Picqer namespace
    if (strpos($class, 'Picqer\\Barcode\\') !== 0) {
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, 15); // Remove 'Picqer\Barcode\'
    
    // Base directory for the class files
    $base_dir = __DIR__ . '/src/';
    
    // Replace namespace separators with directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});