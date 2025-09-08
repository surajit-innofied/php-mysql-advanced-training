<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../config/Db_Connect.php';

class ProductCreationTest extends TestCase
{
    private $pdo;
    private $productController;
    private $adminId;
    private $categoryId;

    protected function setUp(): void
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->productController = new ProductController();

        // Reset tables
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->pdo->exec("TRUNCATE TABLE new_products");
        $this->pdo->exec("TRUNCATE TABLE categories");
        $this->pdo->exec("TRUNCATE TABLE users");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        // Insert dummy admin
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(["Admin Tester", "admin@test.com", password_hash("password", PASSWORD_BCRYPT), "admin"]);
        $this->adminId = $this->pdo->lastInsertId();

        // Insert dummy category
        $stmt = $this->pdo->prepare("INSERT INTO categories (name, type) VALUES (?, ?)");
        $stmt->execute(["Electronics", "physical"]);
        $this->categoryId = $this->pdo->lastInsertId();

        // Fake session for controller
        $_SESSION = [
            'user' => ['id' => $this->adminId, 'email' => 'admin@test.com'],
            'role' => 'admin'
        ];
    }

    public function test_admin_can_add_physical_product()
    {
        $post = [
            'email'         => 'product@test.com',
            'name'          => 'Test Laptop',
            'price'         => 1000.00,
            'category'      => $this->categoryId,
            'category_type' => 'physical',
            'weight'        => 1.5,
            'stock'         => 10,
        ];

        $files = []; // no file upload for physical

        $errors = $this->productController->add($post, $files);

        $this->assertEmpty($errors, "Expected no validation/database errors. Got: " . print_r($errors, true));

        $stmt = $this->pdo->prepare("SELECT * FROM new_products WHERE name = ?");
        $stmt->execute([$post['name']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($product, "Product should exist in database.");
        $this->assertEquals($post['name'], $product['name']);
    }
}
