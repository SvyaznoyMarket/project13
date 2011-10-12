<?php

class UserProductCompare extends BaseUserData
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

    if (empty($products[$product->type_id]))
    {
      $products[$product->type_id] = array();
    }
    $products[$product->type_id][$product->id] = true;

    $this->parameterHolder->set('products', $products);
  }

  public function deleteProduct($type_id, $product_id)
  {
    $products = $this->parameterHolder->get('products');

    if (isset($products[$type_id][$product_id]))
    {
      unset($products[$type_id][$product_id]);
      if (0 == count($products[$type_id]))
      {
        unset($products[$type_id]);
      }
    }

    $this->parameterHolder->set('products', $products);
  }

  public function hasProduct($type_id, $id)
  {
    $products = $this->parameterHolder->get('products');

    return isset($products[$type_id][$id]);
  }

  public function getProducts($type_id)
  {
    if (empty($type_id))
    {
      throw new InvalidArgumentException('You must provide a type_id.');
    }

    $table = ProductTable::getInstance();

    $productList = $table->createList();
    $products = $this->parameterHolder->get('products');
    if (!empty($products[$type_id]))
    {
      foreach ($products[$type_id] as $id => $data)
      {
        $product = $table->getById($id, array(
          'view'           => 'show',
          'group_property' => true,
        ));
        $productList[] = $product;
      }
    }

    return $productList;
  }

  public function clear($type_id = null)
  {
    $products = $this->parameterHolder->get('products');
    if (null != $this->products)
    {
      if (null == $type_id)
      {
        $this->products->free();
        $this->products = null;
        $products = array();
      }
      else
      {
        $this->products[$type_id]->free();
        $this->products[$type_id] = null;
        unset($products[$type_id]);
      }
    }

    $this->parameterHolder->set('products', $products);
  }

  public function hasProductType($type_id)
  {
    $products = $this->parameterHolder->get('products');

    return isset($products[$type_id]);
  }

  public function getProductTypes()
  {
    $table = ProductTypeTable::getInstance();

    $productTypeList = $table->createList();
    foreach ($this->parameterHolder->get('products') as $type_id => $data)
    {
      $productType = $table->getById($type_id);
      $productTypeList[] = $productType;
    }

    return $productTypeList;
  }
  
  public function getProductsNum(){
      $num = 0;
      $params = $this->parameterHolder->getParameters();
      foreach ($params['products'] as $prod){
          $num += count($prod);
      }
      return $num;
  }
}