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

// Fetch all users with their build count
try {
    $stmt = $pdo->query("
        SELECT 
            u.*,
            COUNT(b.id) as build_count
        FROM users u
        LEFT JOIN builds b ON u.id = b.user_id
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $users = [];
    $error = "Error loading users: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBUILD - User Management</title>
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
            grid-template-columns: repeat(3, 1fr);
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

        .users-table {
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

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .user-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-admin {
            background: #ffc107;
            color: #000;
        }

        .badge-user {
            background: #e3f2fd;
            color: #1976d2;
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
            <h1><i class="fas fa-users"></i> User Management</h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p><?php echo count($users); ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Builders</h3>
                <p><?php echo count(array_filter($users, function($u) { return $u['build_count'] > 0; })); ?></p>
            </div>
            <div class="stat-card">
                <h3>Newest Member</h3>
                <p><?php echo !empty($users) ? htmlspecialchars($users[0]['username']) : 'N/A'; ?></p>
            </div>
        </div>

        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Builds</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                        <tr>
                            <td>#<?php echo $user['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                <?php if($user['id'] == $_SESSION['user_id']): ?>
                                    <span class="user-badge badge-admin">YOU</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo $user['build_count']; ?> builds</td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if($user['id'] != $_SESSION['user_id']): ?>
                                    <button class="btn btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 12px;">Cannot delete yourself</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function deleteUser(id, username) {
            if(!confirm(`Are you sure you want to delete user "${username}"? This will also delete all their builds.`)) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'user-actions.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>