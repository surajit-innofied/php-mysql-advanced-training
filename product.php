<?php
class Product
{
    private $pdo;

    public $id;
    public $email;
    public $name;
    public $price;
    public $category_id;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Add new product
    public function addProduct()
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO new_products (email, name, price, category_id)
            VALUES (:email, :name, :price, :category_id)
        ");
        $stmt->execute([
            ':email' => $this->email,
            ':name'  => $this->name,
            ':price' => $this->price,
            ':category_id' => $this->category_id
        ]);
    }

    // Update existing product
    public function updateProduct($id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE new_products 
            SET email = :email, name = :name, price = :price, category_id = :category_id
            WHERE id = :id
        ");
        $stmt->execute([
            ':email' => $this->email,
            ':name'  => $this->name,
            ':price' => $this->price,
            ':category_id' => $this->category_id,
            ':id'    => $id
        ]);
    }

    // Delete product
    public function deleteProduct() {
    $stmt = $this->pdo->prepare("DELETE FROM new_products WHERE id = ?");
    $ok = $stmt->execute([$this->id]);
    if (!$ok) {
        print_r($stmt->errorInfo());
    }
    return $ok;
}

    // Get all products
    public function getAllProducts()
    {
        $stmt = $this->pdo->query("
            SELECT p.id, p.email, p.name, p.price, c.name AS category
            FROM new_products p
            LEFT JOIN catagoreis c ON p.category_id = c.id
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single product by ID
    public function getProductById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM new_products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
