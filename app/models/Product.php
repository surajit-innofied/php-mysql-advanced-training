<?php
class Product
{
    protected $name;
    protected $email;
    protected $categoryType;
    protected $category;
    protected $price;

    protected $stock;   // 👈 new property



    public function __construct($name, $email, $categoryType, $category, $price, $stock = 0)
    {
        $this->name = $name;
        $this->email = $email;
        $this->categoryType = $categoryType;
        $this->category = $category;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function getName()
    {
        return $this->name;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getCategoryType()
    {
        return $this->categoryType;
    }
    public function getCategory()
    {
        return $this->category;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getStock()
    {
        return $this->stock;
    }  // 👈 getter
}
