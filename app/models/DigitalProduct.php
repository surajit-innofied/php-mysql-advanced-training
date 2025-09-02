<?php
require_once "Product.php";

class DigitalProduct extends Product
{
    private $fileLink;

    public function __construct($name, $email, $categoryType, $category, $price, $fileLink, $stock = 0)
    {
        parent::__construct($name, $email, $categoryType, $category, $price, $stock);
        $this->fileLink = $fileLink;
    }

    public function getFileLink()
    {
        return $this->fileLink;
    }
}
