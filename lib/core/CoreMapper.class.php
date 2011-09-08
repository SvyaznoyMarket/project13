<?php

class CoreMapper
{
  public function getModel($name)
  {
    $models = array(
      'category' => 'ProductCategory',
      'order'    => 'Order',
      'product'  => 'Product',
      'shop'     => 'Shop',
    );
  }
}