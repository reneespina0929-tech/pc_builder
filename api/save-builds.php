<?php
session_start();
require_once '../includes/config.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Please login first']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if(!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid data received']);
    exit();
}

$user_id = $_SESSION['user_id'];
$build_name = isset($data['build_name']) ? trim($data['build_name']) : 'My Build';
$components = isset($data['components']) ? $data['components'] : [];
$total_price = isset($data['total_price']) ? floatval($data['total_price']) : 0;
$total_wattage = isset($data['total_wattage']) ? intval($data['total_wattage']) : 0;

// Validation
if(empty($components)) {
    echo json_encode(['success' => false, 'error' => 'Please add at least one component']);
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert into builds table
    $stmt = $pdo->prepare("
        INSERT INTO builds (user_id, build_name, total_price, total_wattage) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $build_name, $total_price, $total_wattage]);
    
    // Get the build ID
    $build_id = $pdo->lastInsertId();
    
    // Insert components
    $stmt = $pdo->prepare("
        INSERT INTO build_components (build_id, component_type, product_id, product_name, product_specs, price, wattage) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach($components as $component) {
        $stmt->execute([
            $build_id,
            $component['type'],
            $component['id'],
            $component['name'],
            $component['specs'] ?? '',
            $component['price'],
            $component['wattage']
        ]);
    }
    
    $pdo->commit();
    
    // SUCCESS RESPONSE
    echo json_encode([
        'success' => true, 
        'message' => 'Build saved successfully!',
        'build_id' => $build_id,
        'build_name' => $build_name
    ]);
    exit();
    
} catch(PDOException $e) {
    // Rollback on error
    if($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Return error but with more details for debugging
    echo json_encode([
        'success' => false, 
        'error' => 'Database error occurred',
        'details' => $e->getMessage() // Remove this in production
    ]);
    exit();
}
?>