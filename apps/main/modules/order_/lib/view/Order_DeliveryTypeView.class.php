<?php

class Order_DeliveryTypeView
{
  public $id;
  public $type;
  public $token;
  public $name;
  public $shortName;
  public $description;
  public $date;
  public $interval;

  /* @var $shop Order_ShopView */
  public $shop;

  /* @var $items Order_ItemView[] */
  public $items = array();


}