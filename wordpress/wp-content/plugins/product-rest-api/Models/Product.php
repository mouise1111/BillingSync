<?php

class Product
{
  private int $id;
  private $title;
  private $type;
  private $productCategoryId;
  private string $custom_1;

  // Constructor to initialize the product
  public function __construct($title, $type, $productCategoryId = null, string $custom_1 = null)
  {
    $this->title = $title;
    $this->type = $type;
    $this->productCategoryId = $productCategoryId;

    // Only generate a new custom_1 if the passed custom_1 is null
    if ($custom_1 === null) {
      $this->custom_1 = uniqid('wp_');
    } else {
      $this->custom_1 = $custom_1;
    }
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

  public function getCustom1() {
    return $this->custom_1;
  }

  public function setCustom1($custom_1) {
    $this->custom_1 = $custom_1;
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
