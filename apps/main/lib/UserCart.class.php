<?php

class UserCart extends BaseUserData
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

  public function addProduct(Product $product, $quantity = 1)
  {
    $products = $this->parameterHolder->get('products');

    if (!isset($products[$product->id]) || empty($products[$product->id]))
    {
      $products[$product->id] = $this->getDefaults();
    }
    $products[$product->id]['quantity'] = $quantity;
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

  public function addService(Product $product, Service $service, $quantity = 1)
  {
    $products = $this->parameterHolder->get('products');

    if (!isset($products[$product->id]) || empty($products[$product->id]))
    {
      return false;
    }
    if (!isset($products[$product->id]['service'][$service->id]) || empty($products[$product->id]['service'][$service->id]))
    {
      $products[$product->id]['service'][$service->id] = array('quantity' => 0, );
    }
    $products[$product->id]['service'][$service->id]['quantity'] += $quantity;
    $this->parameterHolder->set('products', $products);
    $this->calculateDiscount();
  }

  public function deleteService(Product $product, Service $service)
  {
    $products = $this->parameterHolder->get('products');

    if (isset($products[$product->id]['service'][$service->id]))
    {
      unset($products[$product->id]['service'][$service->id]);
      $this->parameterHolder->set('products', $products);
      $this->calculateDiscount();
    }
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

  public function hasProduct($id)
  {
    $products = $this->parameterHolder->get('products');

    return isset($products[$id]);
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
    $count = count($this->parameterHolder->get('products'));
    return $count;
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
	  myDebug::dump($products[$key]);
      $this->updateProductCart($product, 'quantity', $products[$key]['quantity']);
      $this->updateProductCart($product, 'service', $products[$key]['service']);
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
      $cart = $this->getDefaults();
    }
    $cart[$property] = $value;

    $product->mapValue('cart', $cart);
  }

  protected function getServiceById($id)
  {
    return array();
  }

  protected function getWarrantyById($id)
  {
    return array();
  }

  protected function getDefaults()
  {
    return array(
      'quantity' => 1,
      'discount' => 0,
      'service' => array(),
      'warranty' => array(),
    );
  }
}

