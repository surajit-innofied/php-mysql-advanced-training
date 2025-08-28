<?php
require_once "product.php";

class PhysicalProduct extends Product {

    public function __construct($pdo, $name, $price, $weight) {
        parent::__construct($pdo);
        $this->setName($name);
        $this->setPrice($price);
        $this->setWeight($weight);  // use parent setter
    }

    public function addProduct() {
        $stmt = $this->pdo->prepare("
            INSERT INTO new_products (email, name, price, category_id, weight)
            VALUES (:email, :name, :price, :category_id, :weight)
        ");

        $stmt->execute([
            ':email' => $this->getEmail(),
            ':name'  => $this->getName(),
            ':price' => $this->getPrice(),
            ':category_id' => $this->getCategoryId(),
            ':weight' => $this->getWeight()
        ]);

        return $this->pdo->lastInsertId();
    }

      public function updateProduct($pdo, $id, $categoryId) {
        $stmt = $pdo->prepare("UPDATE products SET name=?, email=?, price=?, category=?, weight=?, file_link=NULL WHERE id=?");
        return $stmt->execute([$this->name, $this->email, $this->price, $categoryId, $this->weight, $id]);
    }
}
?>
