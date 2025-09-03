<?php
session_start();
require_once __DIR__ . '/../../config/Db_Connect.php';
require '../../vendor/autoload.php'; // Stripe SDK

\Stripe\Stripe::setApiKey(""); // secret key

$orderId = (int)$_GET['order_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    http_response_code(400);
    echo json_encode(["error" => "Order not found"]);
    exit;
}

// Create checkout session
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'inr',
            'product_data' => [
                'name' => "Order #$orderId",
            ],
            'unit_amount' => $order['total_amount'] * 100, // amount in paisa
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => "http://localhost/public/payment_success.php?order_id=$orderId",
    'cancel_url' => "http://localhost/public/payment_failed.php?order_id=$orderId",
]);

echo json_encode(['id' => $session->id]);
