<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

try {
    $category = isset($_GET['category']) ? $_GET['category'] : '';

    if(empty($category)) {
        $stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY category, name");
        $allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group by category
        $grouped = [];
        foreach($allProducts as $product) {
            $cat = $product['category'];
            if(!isset($grouped[$cat])) {
                $grouped[$cat] = [];
            }
            $grouped[$cat][] = [
                'id' => (int)$product['id'],
                'name' => $product['name'],
                'specs' => $product['specs'],
                'price' => (float)$product['price'],
                'wattage' => (int)$product['wattage'],
                'stock' => (int)$product['stock'],
                'image_url' => $product['image_url']
            ];
        }

        echo json_encode([
            'success' => true,
            'data' => $grouped
        ]);

    } else {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND is_active = 1 ORDER BY name");
        $stmt->execute([$category]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $formattedProducts = array_map(function($product) {
            return [
                'id' => (int)$product['id'],
                'name' => $product['name'],
                'specs' => $product['specs'],
                'price' => (float)$product['price'],
                'wattage' => (int)$product['wattage'],
                'stock' => (int)$product['stock'],
                'image_url' => $product['image_url']
            ];
        }, $products);

        echo json_encode([
            'success' => true,
            'data' => $formattedProducts
        ]);
    }

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>