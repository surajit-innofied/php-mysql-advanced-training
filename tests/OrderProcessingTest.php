<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/Db_Connect.php';

class OrderProcessingTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        global $pdo;
        $this->pdo = $pdo;

        // Clean up tables before each test
        $this->pdo->exec("DELETE FROM order_items");
        $this->pdo->exec("DELETE FROM orders");
        $this->pdo->exec("DELETE FROM addresses");
        $this->pdo->exec("DELETE FROM new_products");
        $this->pdo->exec("DELETE FROM users");
    }

    public function test_order_creation_with_paid_status()
    {
        // --- Arrange ---
        // Create test user
        $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)")
            ->execute(["Test User", "testuser@example.com", "pass123", "user"]);
        $userId = $this->pdo->lastInsertId();

        // Create product
        $this->pdo->prepare("INSERT INTO new_products (name, email, price, category_id, stock, created_by) VALUES (?,?,?,?,?,?)")
            ->execute(["Phone", "p@example.com", 100.00, null, 10, $userId]);
        $productId = $this->pdo->lastInsertId();

        // Simulate checkout address
        $this->pdo->prepare("INSERT INTO addresses (user_id, address, city, state, zip, country) VALUES (?,?,?,?,?,?)")
            ->execute([$userId, "123 Street", "Kolkata", "WB", "700001", "India"]);
        $addressId = $this->pdo->lastInsertId();

        // --- Act ---
        $this->pdo->beginTransaction();

        // Insert order
        $this->pdo->prepare("INSERT INTO orders (user_id, address_id, total_amount, statuss, payment_id) VALUES (?,?,?,?,?)")
            ->execute([$userId, $addressId, 200.00, "paid", "test-session-123"]);
        $orderId = $this->pdo->lastInsertId();

        // Insert items
        $this->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?,?,?,?)")
            ->execute([$orderId, $productId, 2, 100.00]);

        // Reduce stock
        $this->pdo->prepare("UPDATE new_products SET stock = stock - ? WHERE id = ?")->execute([2, $productId]);

        $this->pdo->commit();

        // --- Assert ---
        // Check order exists
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals("paid", $order['statuss'], "Order should be marked as paid");

        // Check order items
        $stmt = $this->pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $items, "Order should have 1 item");
        $this->assertEquals(2, $items[0]['quantity'], "Order item quantity should match");

        // Check stock reduced
        $stmt = $this->pdo->prepare("SELECT stock FROM new_products WHERE id = ?");
        $stmt->execute([$productId]);
        $stock = $stmt->fetchColumn();

        $this->assertEquals(8, $stock, "Stock should be reduced after order");
    }
}
