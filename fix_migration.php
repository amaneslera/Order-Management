<?php
// Fix migration issue by marking activity_logs migration as completed
require 'vendor/autoload.php';

// Bootstrap CodeIgniter
$pathsPath = realpath(FCPATH . '../app/Config/Paths.php') ?: FCPATH . '../app/Config/Paths.php';
require $pathsPath;
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$db = \Config\Database::connect();

try {
    // Insert the missing migration record
    $data = [
        'version' => '2024-01-01-000006',
        'class' => 'App\\Database\\Migrations\\CreateActivityLogsTable',
        'group' => 'default',
        'namespace' => 'App',
        'time' => time(),
        'batch' => 1
    ];
    
    $db->table('migrations')->insert($data);
    
    echo "✅ Successfully marked CreateActivityLogsTable migration as completed!\n";
    echo "Now you can run: php spark migrate\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
