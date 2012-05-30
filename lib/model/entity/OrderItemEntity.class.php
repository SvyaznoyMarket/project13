<?php

/**
 * Элемент заказа
 */
class OrderItemEntity
{
  const TYPE_PRODUCT = 1;
  const TYPE_SERVICE = 2;

  /* @var integer */
  private $id;

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
}