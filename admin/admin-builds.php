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

// Protect this page
if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?error=Please login to access this page");
    exit();
}

// Fetch all builds with user information
try {
    $stmt = $pdo->query("
        SELECT 
            b.*,
            u.username,
            u.email,
            COUNT(bc.id) as component_count
        FROM builds b
        JOIN users u ON b.user_id = u.id
        LEFT JOIN build_components bc ON b.id = bc.build_id
        GROUP BY b.id
        ORDER BY b.created_at DESC
    ");
    $builds = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $builds = [];
    $error = "Error loading builds: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBUILD - User Builds Management</title>
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            color: #04192F;
            font-size: 28px;
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

        .builds-table {
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

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background: #04192F;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
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

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        <?php if(isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1><i class="fas fa-tools"></i> User Builds Management</h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Builds</h3>
                <p><?php echo count($builds); ?></p>
            </div>
            <div class="stat-card">
                <h3>Avg. Components</h3>
                <p><?php 
                    $avgComponents = count($builds) > 0 ? round(array_sum(array_column($builds, 'component_count')) / count($builds), 1) : 0;
                    echo $avgComponents; 
                ?></p>
            </div>
            <div class="stat-card">
                <h3>Avg. Build Value</h3>
                <p>₱<?php 
                    $avgValue = count($builds) > 0 ? number_format(array_sum(array_column($builds, 'total_price')) / count($builds), 2) : '0.00';
                    echo $avgValue; 
                ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Value</h3>
                <p>₱<?php echo number_format(array_sum(array_column($builds, 'total_price')), 2); ?></p>
            </div>
        </div>

        <div class="builds-table">
            <?php if(empty($builds)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Builds Yet</h3>
                    <p>User builds will appear here when users save their PC configurations</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Build Name</th>
                            <th>User</th>
                            <th>Components</th>
                            <th>Total Price</th>
                            <th>Wattage</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($builds as $build): ?>
                            <tr>
                                <td>#<?php echo $build['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($build['build_name']); ?></strong></td>
                                <td>
                                    <?php echo htmlspecialchars($build['username']); ?>
                                    <br>
                                    <small style="color: #666;"><?php echo htmlspecialchars($build['email']); ?></small>
                                </td>
                                <td><?php echo $build['component_count']; ?> parts</td>
                                <td><strong>₱<?php echo number_format($build['total_price'], 2); ?></strong></td>
                                <td><?php echo $build['total_wattage']; ?>W</td>
                                <td><?php echo date('M d, Y', strtotime($build['created_at'])); ?></td>
                                <td>
                                    <a href="view-build.php?id=<?php echo $build['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button class="btn btn-danger" onclick="deleteBuild(<?php echo $build['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function deleteBuild(id) {
            if(!confirm('Are you sure you want to delete this build?')) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'build-actions.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="build_id" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>