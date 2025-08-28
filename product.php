<?php
/**
 * Product Class
 *
 * Handles CRUD operations for products.
 * Supports both Physical and Digital products
 * with extra fields: weight (physical) and file_link (digital).
 */
class Product
{
    protected $pdo; // Database connection (PDO instance)

    // Common product fields
    protected $id;
    protected $email;
    protected $name;
    protected $price;
    protected $category_id;

    // Extra fields (specific to product type)
    protected $weight;     // Only for physical products (nullable)
    protected $file_link;  // Only for digital products (nullable)

    /**
     * Constructor - requires PDO connection
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /* ----------------- ENCAPSULATION (Getters & Setters) ----------------- */

    // ID
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    // Email
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    // Name
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    // Price
    public function getPrice() { return $this->price; }
    public function setPrice($price) { $this->price = $price; }

    // Category
    public function getCategoryId() { return $this->category_id; }
    public function setCategoryId($category_id) { $this->category_id = $category_id; }

    // Weight (only for physical)
    public function getWeight() { return $this->weight; }
    public function setWeight($weight) { $this->weight = $weight; }

    // File Link (only for digital)
    public function getFileLink() { return $this->file_link; }
    public function setFileLink($file_link) { $this->file_link = $file_link; }

    /* ----------------- CRUD OPERATIONS ----------------- */

    /**
     * Add new product
     * Handles both physical (weight) and digital (file_link).
     *
     * @return int Last inserted product ID
     */
    public function addProduct()
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO new_products (email, name, price, category_id, weight, file_link)
            VALUES (:email, :name, :price, :category_id, :weight, :file_link)
        ");

        // Use null when optional fields are not set
        $stmt->execute([
            ':email'      => $this->email,
            ':name'       => $this->name,
            ':price'      => $this->price,
            ':category_id'=> $this->category_id,
            ':weight'     => $this->weight ?? null,
            ':file_link'  => $this->file_link ?? null,
        ]);

        return $this->pdo->lastInsertId(); // Return newly inserted ID
    }

    /**
     * Update product by ID
     * NOTE: In your DB it should update `new_products` (not `products`).
     *
     * @param int $id Product ID
     * @param int $categoryId Category ID
     * @return bool Success/failure
     */
    public function updateProduct($pdo, $id, $categoryId)
    {
        // ⚠️ TODO: You may want to update `new_products` instead of `products`
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name=?, email=?, price=?, category=? 
            WHERE id=?
        ");

        return $stmt->execute([$this->name, $this->email, $this->price, $categoryId, $id]);
    }

    /**
     * Get all products (joined with category details)
     *
     * @return array List of products
     */
    public function getAllProducts()
    {
        $stmt = $this->pdo->query("
            SELECT p.id, p.email, p.name, p.price, p.weight, p.file_link, 
                   c.name AS category_name, c.type AS category_type
            FROM new_products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single product by ID
     *
     * @param int $id Product ID
     * @return array|false Product data or false if not found
     */
    public function getProductById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.type AS category_type 
            FROM new_products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete product by ID
     *
     * @param int $id Product ID
     * @return bool Success/failure
     */
    public function deleteProduct($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM new_products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
