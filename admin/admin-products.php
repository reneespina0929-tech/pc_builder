<?php
session_start();
require_once '../includes/config.php';
// Check if user is admin
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if(!$user || !$user['is_admin']) {
    header("Location: ../pages/homepage.php?error=Access denied - Admin only");
    exit();
}

// Protect this page - require login (you can add admin check later)
if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?error=Please login to access this page");
    exit();
}

// Fetch all products
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY category, name");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $products = [];
    $error = "Error loading products: " . $e->getMessage();
}

// Get unique categories
$categories = ['cpu', 'motherboard', 'cooler', 'ram', 'gpu', 'storage', 'psu', 'case', 'monitor'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBUILD - Product Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif, 'poppins';
        }

        body {
            background-color: #f5f5f5;
        }

        header {
            background-color: #04192F;
            color: white;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 30px;
        }

        .login-register {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .login-register a, .login-register span {
            color: white;
            text-decoration: none;
        }

        nav {
            height: 70px;
            padding: 30px;
            background-color: #103D6E;
        }

        .nav-bar ul {
            display: flex;
            justify-content: start;
            gap: 30px;
            list-style: none;
        }

        .nav-bar ul li a {
            text-decoration: none;
            color: white;
        }

        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 20px;
        }

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            color: #04192F;
            font-size: 28px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #04192F;
            color: white;
        }

        .btn-primary:hover {
            background: #103D6E;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-warning {
            background: #ffc107;
            color: #000;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 32px;
            font-weight: bold;
            color: #04192F;
        }

        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .filters select, .filters input {
            padding: 10px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .filters select {
            min-width: 200px;
        }

        .filters input {
            flex: 1;
        }

        .products-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #04192F;
            color: white;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }

        tbody tr {
            border-bottom: 1px solid #eee;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .category-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-cpu { background: #e3f2fd; color: #1976d2; }
        .badge-motherboard { background: #f3e5f5; color: #7b1fa2; }
        .badge-cooler { background: #e0f2f1; color: #00796b; }
        .badge-ram { background: #fff3e0; color: #f57c00; }
        .badge-gpu { background: #fce4ec; color: #c2185b; }
        .badge-storage { background: #f1f8e9; color: #689f38; }
        .badge-psu { background: #ffe0b2; color: #ef6c00; }
        .badge-case { background: #e8eaf6; color: #3f51b5; }
        .badge-monitor { background: #e0f7fa; color: #0097a7; }

        .stock-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .stock-high { background: #d4edda; color: #155724; }
        .stock-medium { background: #fff3cd; color: #856404; }
        .stock-low { background: #f8d7da; color: #721c24; }

        .actions {
            display: flex;
            gap: 8px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 10000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 40px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .modal-header h2 {
            color: #04192F;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #666;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>UBUILD - Admin Panel</h1>
        <div class="login-register">
            <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
            <a href="../pages/homepage.php">Back to Site</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </header>

    <nav class="nav-bar">
        <ul>
            <li><a href="admin-products.php">Products</a></li>
            <li><a href="admin-builds.php">User Builds</a></li>
            <li><a href="admin-users.php">Users</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error']) || isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error'] ?? $error); ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1><i class="fas fa-box-open"></i> Product Management</h1>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Product
            </button>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?php echo count($products); ?></p>
            </div>
            <div class="stat-card">
                <h3>Categories</h3>
                <p><?php echo count(array_unique(array_column($products, 'category'))); ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Value</h3>
                <p>₱<?php echo number_format(array_sum(array_column($products, 'price')), 2); ?></p>
            </div>
            <div class="stat-card">
                <h3>In Stock</h3>
                <p><?php echo array_sum(array_column($products, 'stock')); ?></p>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <select id="categoryFilter" onchange="filterProducts()">
                <option value="">All Categories</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat; ?>"><?php echo ucfirst($cat); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterProducts()">
        </div>

        <!-- Products Table -->
        <div class="products-table">
            <?php if(empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No Products Yet</h3>
                    <p>Click "Add New Product" to get started</p>
                </div>
            <?php else: ?>
                <table id="productsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Product Name</th>
                            <th>Specs</th>
                            <th>Price</th>
                            <th>Wattage</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                            <tr>
                                <td>#<?php echo $product['id']; ?></td>
                                <td>
                                    <span class="category-badge badge-<?php echo $product['category']; ?>">
                                        <?php echo strtoupper($product['category']); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($product['specs']); ?></td>
                                <td><strong>₱<?php echo number_format($product['price'], 2); ?></strong></td>
                                <td><?php echo $product['wattage']; ?>W</td>
                                <td>
                                    <span class="stock-badge <?php 
                                        echo $product['stock'] > 10 ? 'stock-high' : 
                                            ($product['stock'] > 5 ? 'stock-medium' : 'stock-low'); 
                                    ?>">
                                        <?php echo $product['stock']; ?> units
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <button class="btn btn-warning btn-sm" onclick='editProduct(<?php echo json_encode($product); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Product</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="productForm" method="POST" action="product-actions.php">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="product_id" id="productId">

                <div class="form-group">
                    <label for="category">Category *</label>
                    <select name="category" id="category" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>"><?php echo ucfirst($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="form-group">
                    <label for="specs">Specifications</label>
                    <textarea name="specs" id="specs"></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Price (₱) *</label>
                    <input type="number" step="0.01" name="price" id="price" required>
                </div>

                <div class="form-group">
                    <label for="wattage">Wattage (W)</label>
                    <input type="number" name="wattage" id="wattage" value="0">
                </div>

                <div class="form-group">
                    <label for="stock">Stock Quantity *</label>
                    <input type="number" name="stock" id="stock" required value="0">
                </div>

                <div class="form-group">
                    <label for="image_url">Image URL (optional)</label>
                    <input type="text" name="image_url" id="image_url">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Open add modal
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('formAction').value = 'add';
            document.getElementById('productForm').reset();
            document.getElementById('productModal').classList.add('active');
        }

        // Edit product
        function editProduct(product) {
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('productId').value = product.id;
            document.getElementById('category').value = product.category;
            document.getElementById('name').value = product.name;
            document.getElementById('specs').value = product.specs || '';
            document.getElementById('price').value = product.price;
            document.getElementById('wattage').value = product.wattage;
            document.getElementById('stock').value = product.stock;
            document.getElementById('image_url').value = product.image_url || '';
            document.getElementById('productModal').classList.add('active');
        }

        // Delete product
        function deleteProduct(id, name) {
            if(!confirm(`Are you sure you want to delete "${name}"?`)) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'product-actions.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="product_id" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        // Close modal
        function closeModal() {
            document.getElementById('productModal').classList.remove('active');
        }

        // Filter products
        function filterProducts() {
            const category = document.getElementById('categoryFilter').value.toLowerCase();
            const search = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#productsTable tbody tr');

            rows.forEach(row => {
                const categoryText = row.cells[1].textContent.toLowerCase();
                const nameText = row.cells[2].textContent.toLowerCase();
                const specsText = row.cells[3].textContent.toLowerCase();

                const matchesCategory = !category || categoryText.includes(category);
                const matchesSearch = !search || nameText.includes(search) || specsText.includes(search);

                row.style.display = matchesCategory && matchesSearch ? '' : 'none';
            });
        }
    </script>
</body>
</html>