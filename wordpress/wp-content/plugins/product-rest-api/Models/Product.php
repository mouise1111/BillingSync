<?php

class Product
{
  private int $id;
  private $title;
  private $type;
  private $productCategoryId;

  // Constructor to initialize the product
  public function __construct($title, $type, $productCategoryId = null)
  {
    $this->title = $title;
    $this->type = $type;
    $this->productCategoryId = $productCategoryId;
  }

  // Getter and Setter for Title
  public function getTitle()
  {
    return $this->title;
  }

  public function setTitle($title)
  {
    $this->title = $title;
  }

  // Getter and Setter for Type
  public function getType()
  {
    return $this->type;
  }

  public function setType($type)
  {
    $this->type = $type;
  }

  // Getter and Setter for Product Category ID
  public function getProductCategoryId()
  {
    return $this->productCategoryId;
  }

  public function setProductCategoryId($productCategoryId)
  {
    $this->productCategoryId = $productCategoryId;
  }

  // Getter method for 'id'
  public function getId(): int {
    return $this->id;
  }

  // Setter method for 'id' (used when retrieving from database)
  public function setId(int $id): void {
    $this->id = $id;
  }
  
}
