<?php

class Cart {

  protected $parameterHolder = null;
  protected $products = null;
  protected $defaultCart = array('count' => 1, 'discount' => 0, );

  function __construct($parameters = array())
  {
    $parameters = myToolkit::arrayDeepMerge(array('products' => array(), ), $parameters);
    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add($parameters);
  }

  public function addProduct(Product $product, $count = 1)
  {
    $products = $this->parameterHolder->get('products');

    if (!isset($products[$product->id]) || empty($products[$product->id]))
    {
      $products[$product->id] = array('count' => 1, );
    }
    $products[$product->id]['count'] = $count;
    $this->parameterHolder->set('products', $products);
    $this->calculateDiscount();
  }

  public function getProduct($id)
  {
    $products = $this->parameterHolder->get('products');
    $product = null;

    if (isset($products[$id]) && !empty($products[$id]))
    {
      $this->loadProducts();
      $product = $this->products->get($id);
    }

    return $product;
  }

  public function deleteProduct($id)
  {
    $products = $this->parameterHolder->get('products');

    if (isset($products[$id]))
    {
      unset($products[$id]);
      $this->parameterHolder->set('products', $products);
      $this->calculateDiscount();
    }
  }

  public function clear()
  {
    if (!is_null($this->products))
    {
      $this->products->free();
      $this->products = null;
      $this->parameterHolder->set('products', array());
    }
  }

  public function hasProduct(Product $product)
  {
    $products = $this->parameterHolder->get('products');

    return isset($products[$product->id]) && !empty($products[$product->id]);
  }

  public function getWeight()
  {

  }

  public function getTotal()
  {

  }

  public function getProducts()
  {
    $this->calculateDiscount();
    return $this->products;
  }

  public function count()
  {
    return count($this->parameterHolder->get('products'));
  }

  public function dump()
  {
    return $this->getParameterHolder()->getAll();
  }

  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }

  protected function calculateDiscount()
  {
    $this->loadProducts();

    foreach ($this->products as $product)
    {
      $this->updateProductCart($product, 'discount', 0);
    }
  }

  protected function loadProducts($force = false)
  {
    $products = $this->parameterHolder->get('products');
    $productIds = array_keys($products);
    $productTable = ProductTable::getInstance();

    if (is_null($this->products) || true === $force)
    {
      $this->products = $productTable->createListByIds($productIds, array('index' => array('product' => 'id', ), ));
    }
    else
    {
      $currentIds = $this->products->getKeys();

      $toAddIds = array_diff($productIds, $currentIds);
      $toDelIds = array_diff($currentIds, $productIds);

      $toAdd = $productTable->createListByIds($toAddIds, array('index' => array('product' => 'id', ), ));
      foreach ($toAdd as $key => $product)
      {
        $this->products[$key] = $product;
      }

      foreach ($toDelIds as $id)
      {
        $this->products->remove($id);
      }
    }
    foreach ($this->products as $key => $product)
    {
      $this->updateProductCart($product, 'count', $products[$key]['count']);
    }
  }

  protected function updateProductCart(Doctrine_Record $product, $property, $value)
  {
    if (isset($product->cart))
    {
      $cart = $product->cart;
    }
    else
    {
      $cart = $this->defaultCart;
    }
    $cart[$property] = $value;

    $product->mapValue('cart', $cart);
  }

}

