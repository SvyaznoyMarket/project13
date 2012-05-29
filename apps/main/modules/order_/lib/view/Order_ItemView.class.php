<?php

class Order_ItemView
{
  const TYPE_PRODUCT = 'product';
  const TYPE_SERVICE = 'service';

  public $id;
  public $type; // товар или услуга
  public $token;
  public $stock;
  public $name;
  public $image;
  public $price;
  public $quantity;
  public $total;
  public $url;
  public $deleteUrl;

  /* @var $deliveries Order_DeliveryView[] */
  public $deliveries = array();
}