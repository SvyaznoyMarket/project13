<?php

/**
 * Элемент заказа
 */
class OrderItemEntity
{
  const TYPE_PRODUCT = 'product';
  const TYPE_SERVICE = 'service';

  /* @var integer */
  private $id;

  /* @var integer */
  private $type;

  /* @var OrderItem */
  private $order;

  /* @var ProductEntity */
  private $product = null;

  /* @var ServiceEntity */
  private $service = null;

  /* @var integer */
  private $price;

  /* @var integer */
  private $quantity;

  /* @var DateTime */
  private $createdAt;

  public function __construct(array $data = array())
  {
    if (array_key_exists('type', $data))           $this->type             = $data['type'];
    if (array_key_exists('price', $data))          $this->price            = (int)$data['price'];
    if (array_key_exists('quantity', $data))       $this->quantity         = (int)$data['quantity'];

    if (self::TYPE_PRODUCT == $this->type) {
      $this->product = new ProductEntity(array('id' => $data['id']));
    }
    else if (self::TYPE_SERVICE == $this->type) {
      $this->service = new ServiceEntity(array('id' => $data['id']));
    }
  }

  /**
   * @param \DateTime $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }

  /**
   * @return \DateTime
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
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
   * @param \OrderItem $order
   */
  public function setOrder($order)
  {
    $this->order = $order;
  }

  /**
   * @return \OrderItem
   */
  public function getOrder()
  {
    return $this->order;
  }

  /**
   * @param int $price
   */
  public function setPrice($price)
  {
    $this->price = $price;
  }

  /**
   * @return int
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * @param \ProductEntity $product
   */
  public function setProduct($product)
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
   * @param \ServiceEntity $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }

  /**
   * @return \ServiceEntity
   */
  public function getService()
  {
    return $this->service;
  }

  /**
   * @param int $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  /**
   * @return int
   */
  public function getType()
  {
    return $this->type;
  }
}