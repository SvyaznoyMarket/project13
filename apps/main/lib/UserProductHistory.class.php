<?php

class UserProductHistory extends myUserData
{
  protected
    $parameterHolder = null,
    $products = null
  ;

  function __construct($parameters = array())
  {
    $parameters = myToolkit::arrayDeepMerge(array('products' => array(), ), $parameters);
    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add($parameters);
  }

  public function addProduct(Product $product)
  {
    $products = $this->parameterHolder->get('products');

    $products[$product->id] = true;

    $this->parameterHolder->set('products', $products);
  }

  public function getProducts()
  {
    $table = ProductTable::getInstance();

    $productList = $table->createList();
    foreach ($this->parameterHolder->get('products') as $id => $data)
    {
      $product = $table->getById($id);
      $productList[] = $product;
    }

    return $productList;
  }

  public function clear()
  {
    if (null != $this->products)
    {
      $this->products->free();
      $this->products = null;
    }

    $this->parameterHolder->set('products', array());
  }
}