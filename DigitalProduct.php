<?php
require_once "product.php";

class DigitalProduct extends Product {
    private $fileLink;

    public function __construct($pdo, $name, $price, $fileLink) {
        parent::__construct($pdo);
        $this->setName($name);
        $this->setPrice($price);
        $this->fileLink = $fileLink;
    }

    public function addProduct() {
        $stmt = $this->pdo->prepare("
            INSERT INTO new_products (email, name, price, category_id, file_link)
            VALUES (:email, :name, :price, :category_id, :file_link)
        ");

        $stmt->execute([
            ':email' => $this->getEmail(),
            ':name' => $this->getName(),
            ':price' => $this->getPrice(),
            ':category_id' => $this->getCategoryId(),
            ':file_link' => $this->fileLink
        ]);

        return $this->pdo->lastInsertId();
    }

    public function updateProduct($pdo, $id, $categoryId) {
        $stmt = $pdo->prepare("UPDATE products SET name=?, email=?, price=?, category=?, file_link=?, weight=NULL WHERE id=?");
        return $stmt->execute([$this->name, $this->email, $this->price, $categoryId, $this->fileLink, $id]);
    }
}
?>
