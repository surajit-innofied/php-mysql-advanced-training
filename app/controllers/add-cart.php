<?php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';

if (!empty($_SESSION['flash_error'])): ?>
    <p style="color:#c0392b; text-align:center;"><?= htmlspecialchars($_SESSION['flash_error']) ?></p>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif;



// only logged-in users
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'user') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];

    $stmt = $pdo->prepare("SELECT id, name, price, stock FROM new_products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: index.php");
        exit;
    }

    if ((int)$product['stock'] <= 0) {
        // can't add out-of-stock
        $_SESSION['flash_error'] = 'This product is out of stock.';
        header("Location: index.php");
        exit;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // if in cart, increase by 1 but cap at stock
    if (isset($_SESSION['cart'][$productId])) {
        $currentQty = (int)$_SESSION['cart'][$productId]['quantity'];
        $newQty = min($currentQty + 1, (int)$product['stock']);
        $_SESSION['cart'][$productId]['quantity'] = $newQty;
    } else {
        $_SESSION['cart'][$productId] = [
            'id' => (int)$product['id'],
            'name' => $product['name'],
            'price' => (float)$product['price'],
            'quantity' => 1
        ];
    }

    header("Location: /../app/views/cart.php");
    exit;
}

header("Location: ../../public/login.php");
exit;
