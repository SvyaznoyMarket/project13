<?php

class Order_DeliveryMapView
{
  /* @var $deliveryTypes Order_DeliveryTypeView[] */
  public $deliveryTypes = array();

  /* @var $items Order_ItemView[] */
  public $items = array();

  /* @var $shops Order_ShopView[] */
  public $shops = array();

  /* @var $unavailable array */
  public $unavailable = array();
}