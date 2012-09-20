<?php

/**
 * Элемент заказа
 */
class OrderItemEntity
{
  const TYPE_PRODUCT = 1;
  const TYPE_SERVICE = 2;

  /* @var integer */
  private $orderId;

  /* @var integer */
  private $serviceId;

  /* @var integer */
  private $productId;

  /* @var float */
  private $price;

  /* @var integer */
  private $quantity;

  /* @var string example: 2011-10-22 22:24:48 */
  private $createdAt;

  public function __construct($data = array()){
    if(array_key_exists('order_id', $data))      $this->orderId    = (int)$data['order_id'];
    if(array_key_exists('product_id', $data))    $this->productId  = (int)$data['product_id'];
    if(array_key_exists('service_id', $data))    $this->serviceId  = (int)$data['service_id'];
    if(array_key_exists('price', $data))         $this->price      = (float)$data['price'];
    if(array_key_exists('quantity', $data))      $this->quantity   = (int)$data['quantity'];
    if(array_key_exists('added', $data))         $this->createdAt  = $data['added'];

  }

  /**
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }

  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * @param int $orderId
   */
  public function setOrderId($orderId)
  {
    $this->orderId = $orderId;
  }

  /**
   * @return int
   */
  public function getOrderId()
  {
    return $this->orderId;
  }

  /**
   * @param float $price
   */
  public function setPrice($price)
  {
    $this->price = $price;
  }

  /**
   * @return float
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * @param int $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
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
    $this->quantity = $quantity;
  }

  /**
   * @return int
   */
  public function getQuantity()
  {
    return $this->quantity;
  }

  /**
   * @param int $serviceId
   */
  public function setServiceId($serviceId)
  {
    $this->serviceId = $serviceId;
  }

  /**
   * @return int
   */
  public function getServiceId()
  {
    return $this->serviceId;
  }
}