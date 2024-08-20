<?php

class Order
{
  private $id;
  private $client_custom_1;
  private $product_custom_1;
  private $price; 

  public function __construct($client_custom_1 = null, $product_custom_1 = null, $price = null)
  {
    $this->id = null;
    $this->client_custom_1 = $client_custom_1 ?? uniqid('wc');
    $this->product_custom_1 = $product_custom_1 ?? uniqid('wp');
    $this->price = $price; 
  }

  // Getters
  public function getId()
  {
    return $this->id;
  }

  public function getClientCustom1()
  {
    return $this->client_custom_1;
  }

  public function getProductCustom1()
  {
    return $this->product_custom_1;
  }

  public function getPrice()
  {
    return $this->price; 
  }

  // Setters
  public function setId($id)
  {
    $this->id = $id;
  }

  public function setClientCustom1($client_custom_1)
  {
    $this->client_custom_1 = $client_custom_1;
  }

  public function setProductCustom1($product_custom_1)
  {
    $this->product_custom_1 = $product_custom_1;
  }

  public function setPrice($price)
  {
    $this->price = $price;
  }
}

