<?php

class DeliveryTypeEntity
{
  /* @var integer */
  private $id;

  /* @var string */
  private $token;

  /* @var string */
  private $name;

  /* @var string */
  private $description;

  /* @var ProductEntity[] */
  private $product = array();

  /* @var ShopEntity[] */
  private $shop = array();


  public function __toString()
  {
    return (string)$this->name;
  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  public function setProduct(array $product)
  {
    $this->product = $product;
  }

  public function addProduct(ProductEntity $product)
  {
    $this->product[] = $product;
  }

  public function getProduct()
  {
    return $this->product;
  }

  public function setShop(array $shop)
  {
    $this->shop = $shop;
  }

  public function addShop(ShopEntity $shop)
  {
    $this->shop[] = $shop;
  }

  /**
   * @return ShopEntity
   */
  public function getShop()
  {
    return $this->shop;
  }

  /**
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }

  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
}