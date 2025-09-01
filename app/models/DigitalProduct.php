<?php
require_once "Product.php";

class DigitalProduct extends Product {
    private $fileLink;

    public function __construct($name, $email, $categoryType, $category, $price, $fileLink) {
        parent::__construct($name, $email, $categoryType, $category, $price);
        $this->fileLink = $fileLink;
    }

    public function getFileLink() { return $this->fileLink; }
}
?>