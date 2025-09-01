<?php
require_once "Product.php";

class PhysicalProduct extends Product {
    private $weight;

    public function __construct($name, $email, $categoryType, $category, $price, $weight) {
        parent::__construct($name, $email, $categoryType, $category, $price);
        $this->weight = $weight;
    }

    public function getWeight() { return $this->weight; }
}
?>