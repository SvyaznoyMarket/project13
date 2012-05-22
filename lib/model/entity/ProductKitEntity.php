<?php

class ProductKitEntity
{
  /** @var int */
  private $productId;
  /** @var ProductEntity */
  private $product;
  /** @var int */
  private $quantity;

  public function __construct(array $data = array())
  {
    if(isset($data['id'])) $this->setProductId($data['id']);
    if(isset($data['quantity'])) $this->setQuantity($data['quantity']);
  }

  /**
   * @param \ProductEntity $product
   */
  public function setProduct(ProductEntity $product)
  {
    $this->product = $product;
  }

  /**
   * @return \ProductEntity
   */
  public function getProduct()
  {
    return $this->product;
  }

  /**
   * @param int $productId
   */
  public function setProductId($productId)
  {
    $this->productId = (int)$productId;
  }

  /**
   * @return int
   */
  public function getProductId()
  {
    return $this->productId;
  }

  /**
   * @param int $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = (int)$quantity;
  }

  /**
   * @return int
   */
  public function getQuantity()
  {
    return $this->quantity;
  }
}
