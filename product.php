<?php
class Product
{
    protected $pdo;

    // Common product fields
    protected $id;
    protected $email;
    protected $name;
    protected $price;
    protected $category_id;

    // Extra fields (for Physical/Digital)
    protected $weight;     // for physical products (nullable)
    protected $file_link;  // for digital products (nullable)

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Encapsulation: Getters & Setters
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPrice()
    {
        return $this->price;
    }
    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    // Extra fields getters/setters
    public function getWeight()
    {
        return $this->weight;
    }
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    public function getFileLink()
    {
        return $this->file_link;
    }
    public function setFileLink($file_link)
    {
        $this->file_link = $file_link;
    }

    // Add new product (handles extra fields if provided)
    public function addProduct()
    {
        $stmt = $this->pdo->prepare("\n            INSERT INTO new_products (email, name, price, category_id, weight, file_link)\n            VALUES (:email, :name, :price, :category_id, :weight, :file_link)\n        ");

        // use null coalescing to pass NULL when extra fields are not set
        $stmt->execute([
            ':email' => $this->email,
            ':name'  => $this->name,
            ':price' => $this->price,
            ':category_id' => $this->category_id,
            ':weight' => $this->weight ?? null,
            ':file_link' => $this->file_link ?? null,
        ]);

        // return last inserted id for convenience
        return $this->pdo->lastInsertId();
    }

    // Update existing product (updates extra fields too)
    public function updateProduct($pdo, $id, $categoryId)
    {
        $stmt = $pdo->prepare("UPDATE products SET name=?, email=?, price=?, category=? WHERE id=?");
        return $stmt->execute([$this->name, $this->email, $this->price, $categoryId, $id]);
    }

    // Get all products (includes extra fields)
    public function getAllProducts()
    {
        $stmt = $this->pdo->query("\n            SELECT p.id, p.email, p.name, p.price, p.weight, p.file_link, \n                   c.name AS category_name, c.type AS category_type\n            FROM new_products p\n            LEFT JOIN categories c ON p.category_id = c.id\n            ORDER BY p.id DESC\n        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single product by ID
    public function getProductById($id)
    {
        $stmt = $this->pdo->prepare("\n            SELECT p.*, c.type AS category_type \n            FROM new_products p\n            LEFT JOIN categories c ON p.category_id = c.id\n            WHERE p.id = :id\n        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     // Delete product
    public function deleteProduct($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM new_products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
