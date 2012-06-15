<?php

class Order_GaItem
{
  public $orderNumber;
  public $article;
  public $name;
  public $categoryName;
  public $price;
  public $quantity;

  public function __toString()
  {
    $values = array();
    foreach (array(
      'orderNumber',
      'article',
      'name',
      'categoryName',
      'price',
      'quantity',
    ) as $k) {
      $values[] = "'".addslashes($this->{$k})."'";
    }

    return implode(',', $values);
  }
}