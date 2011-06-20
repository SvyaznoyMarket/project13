<?php

class UserProductCompare extends myUserData
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

    if (empty($products[$product->category_id]))
    {
      $products[$product->category_id] = array();
    }
    $products[$product->category_id][$product->id] = true;

    $this->parameterHolder->set('products', $products);
  }

  public function deleteProduct($category_id, $product_id)
  {
    $products = $this->parameterHolder->get('products');

    if (isset($products[$category_id][$product_id]))
    {
      unset($products[$category_id][$product_id]);
      if (0 == count($products[$category_id]))
      {
        unset($products[$category_id]);
      }
    }

    $this->parameterHolder->set('products', $products);
  }

  public function hasProduct($category_id, $id)
  {
    $products = $this->parameterHolder->get('products');

    return isset($products[$category_id][$id]);
  }

  public function getProducts($category_id)
  {
    if (empty($category_id))
    {
      throw new InvalidArgumentException('You must provide a category_id.');
    }

    $table = ProductTable::getInstance();

    $productList = $table->createList();
    $products = $this->parameterHolder->get('products');
    if (!empty($products[$category_id]))
    {
      foreach ($products[$category_id] as $id => $data)
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

  public function clear($category_id = null)
  {
    $products = $this->parameterHolder->get('products');
    if (null != $this->products)
    {
      if (null == $category_id)
      {
        $this->products->free();
        $this->products = null;
        $products = array();
      }
      else
      {
        $this->products[$category_id]->free();
        $this->products[$category_id] = null;
        unset($products[$category_id]);
      }
    }

    $this->parameterHolder->set('products', $products);
  }

  public function hasProductCategory($category_id)
  {
    $products = $this->parameterHolder->get('products');

    return isset($products[$category_id]);
  }

  public function getProductCategories()
  {
    $table = ProductCategoryTable::getInstance();

    $productCategoryList = $table->createList();
    foreach ($this->parameterHolder->get('products') as $category_id => $data)
    {
      $productCategory = $table->getById($category_id);
      $productCategoryList[] = $productCategory;
    }

    return $productCategoryList;
  }
}