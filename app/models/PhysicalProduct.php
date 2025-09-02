<?php
require_once "Product.php";

class PhysicalProduct extends Product
{
    private $weight;

    public function __construct($name, $email, $categoryType, $category, $price, $weight, $stock = 0)
    {
        parent::__construct($name, $email, $categoryType, $category, $price, $stock);
        $this->weight = $weight;
    }

    public function getWeight()
    {
        return $this->weight;
    }
}
