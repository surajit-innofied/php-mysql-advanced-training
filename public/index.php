<?php
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';

$userController = new UserController();
$controller = new ProductController();

// Handle delete (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && $userController->isLoggedIn()) {
    if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin') {
        $controller->delete((int)$_POST['delete_id']);
        echo "<p style='color:green; text-align:center;'>Product deleted successfully!</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Access denied. Admins only.</p>";
    }
}

// Logout
if (isset($_GET['logout'])) {
    $userController->logout();
}

// Fetch product list
$products = $controller->list();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Ecommerce - Product Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h1 {
            text-align: center;
            background: #04AA6D;
            color: white;
            margin: 0;
            padding: 10px;
        }

        h2 {
            margin-top: 30px;
            text-align: center;
            color: #04AA6D;
        }

        p {
            text-align: center;
            font-size: 16px;
        }

        a {
            color: #04AA6D;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .btn {
            padding: 8px 16px;
            background: #04AA6D;
            color: white !important;
            text-decoration: none;
            border-radius: 4px;
            transition: 0.3s;
            display: inline-block;
        }

        .btn:hover {
            background: #028a56;
        }

        /* Login button - Blue */
        .btn.login {
            background: #3498db;
        }

        .btn.login:hover {
            background: #217dbb;
        }

        /* Sign Up button - Orange */
        .btn.signup {
            background: #e67e22;
        }

        .btn.signup:hover {
            background: #ca5c0c;
        }

        /* Profile button - Purple */
        .btn.profile {
            background: #9b59b6;
        }

        .btn.profile:hover {
            background: #7d3c98;
        }

        /* Cart button - Teal */
        .btn.cart {
            background: #16a085;
        }

        .btn.cart:hover {
            background: #117864;
        }

        /* Orders button - Dark Blue */
        .btn.orders {
            background: #2c3e50;
        }

        .btn.orders:hover {
            background: #1a242f;
        }

        .container {
            width: 90%;
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 30px 0;
        }
    </style>
     
</head>

<body>
    <h1>Ecommerce - Product Management</h1>

    <div class="container">
        <?php if ($userController->isLoggedIn()): ?>
            <?php
            $user = $_SESSION['user'];
            $role = isset($user['role']) ? strtolower($user['role']) : 'user';
            $profileUrl = ($role === 'admin')
                ? "../app/views/admin-dashboard.php"
                : "../app/views/user-dashboard.php";
            ?>

            <p>
                Welcome, <?= htmlspecialchars($user['name']) ?>
                (<?= htmlspecialchars(strtoupper($role)) ?>)
                |
                <a href="<?= $profileUrl ?>" class="btn profile btn-primary">Profile</a>

                <?php if ($role === 'user'): ?>
                    |
                    <a href="../app/views/show_cart.php" class="btn ">View Cart</a>
                    |
                    <a href="../app/views/orders.php" class="btn orders">Orders</a>
                <?php elseif ($role === 'admin'): ?>
                    |
                    <a href="../app/views/add-product.php" class="btn">Add Product</a>
                    |
                    <a href="../app/views/admin_reports.php" class="btn">Reports</a>

                <?php endif; ?>

                |
                <a href="index.php?logout=1" class="btn btn danger">Logout</a>
            </p>

        <?php else: ?>
            <p><a href="../app/views/login.php" class="btn login">Login</a></p>
            <p><a href="../app/views/signup.php" class="btn signup">Sign Up</a></p>
        <?php endif; ?>
    </div>


    <hr>
    <h2>All Products</h2>
    <?php include __DIR__ . '/../app/views/product-list.php'; ?>
    </div>
</body>

</html>