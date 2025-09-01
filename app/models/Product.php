<?php
class Product {
    protected $name;
    protected $email;
    protected $categoryType;
    protected $category;
    protected $price;

    public function __construct($name, $email, $categoryType, $category, $price) {
        $this->name = $name;
        $this->email = $email;
        $this->categoryType = $categoryType;
        $this->category = $category;
        $this->price = $price;
    }

    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getCategoryType() { return $this->categoryType; }
    public function getCategory() { return $this->category; }
    public function getPrice() { return $this->price; }
}
?>