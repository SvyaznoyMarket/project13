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
    $newQty = $products[$product->id]['service'][$service->id]['quantity'] + $quantity;
    if ($newQty < 0) $newQty = 0;
    if ($newQty > $products[$product->id]['quantity']) $newQty = $products[$product->id]['quantity'];

    if ($newQty == 0){
        unset($products[$product->id]['service'][$service->id]);
    } else {
        $products[$product->id]['service'][$service->id]['quantity'] = $newQty;
    }

    $this->parameterHolder->set('products', $products);
    $this->calculateDiscount();
  }

  public function getServicesByProductId($productId)
  {
    $list = array();
    foreach ($this->getProducts() as $product)
    {
         if ($product->id != $productId){
            continue;
         }
          $services = $product->getServiceList();
          foreach ($services as $service)
          {
            //$serviceAr = $service->toArray(false);
            $qty = isset($product['cart']['service'][$service->id]['quantity']) ? $product['cart']['service'][$service->id]['quantity'] : 0;
            if ($qty > 0 ){
                $list[$service->id] = array(
                  'name'      => $service->name,
                  'id'        => $service->id,
                  'token'     => $service->token,
                  //'price'     => (isset($serviceAr['Price'][0])) ? $serviceAr['Price'][0]['price'] : 0,
                  'price'     => (isset($service['Price'][0])) ? $service['Price'][0]['price'] : 0,
                  //'priceFormatted'   => (isset($serviceAr['Price'][0])) ? number_format($serviceAr['Price'][0]['price'], 0, ',', ' ') : 0,
                  'priceFormatted'   => (isset($service['Price'][0])) ? number_format($service['Price'][0]['price'], 0, ',', ' ') : 0,
                  'quantity'  => $qty,
                );
            }
          }
    }
    return $list;

  }

  public function getProductServiceList($getAllServices = false){

    $list = array();
    foreach ($this->getProducts() as $product)
    {
      $services = $product->getServiceList();
      $service_for_list = array();
      foreach ($services as $service)
      {
        $serviceAr = $service->toArray();
        $qty = isset($product['cart']['service'][$service->id]['quantity']) ? $product['cart']['service'][$service->id]['quantity'] : 0;
        if ($qty > 0 || $getAllServices === true){
            $service_for_list[$service->token] = array(
              'name'      => $service->name,
              'id'        => $service->id,
              'token'     => $service->token,
              'price'     => (isset($serviceAr['Price'][0])) ? $serviceAr['Price'][0]['price'] : 0,
              'priceFormatted'   => (isset($serviceAr['Price'][0])) ? number_format($serviceAr['Price'][0]['price'], 0, ',', ' ') : 0,
              'quantity'  => $qty,
            );
        }
      }

      $list[] = array(
        'id'      => $product->id,
        'token'      => $product->token,
        'name'      => $product->name,
        'quantity'  => $product['cart']['quantity'],
        'service'   => $service_for_list,
        'product'   => $product,
        'price'     => $product->price,
        'priceFormatted'     => $product->getFormattedPrice(),
        'total'     => $product['cart']['formatted_total'],
        'photo'     => $product->getMainPhotoUrl(1),
      );
    }
    return $list;
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

  public function getTotal($is_formatted = false)
  {
    $total = 0;
    $products = $this->getProductServiceList();

    //$products = null;
    foreach ($products as $product)
    {
        $total += $product['price'] * $product['quantity'];
        foreach($product['service'] as $service){
            $total += $service['price'] * $service['quantity'];
        }
    }

    $result = $is_formatted ? number_format($total, 0, ',', ' ') : $total;

    //myDebug::dump($result, 1);

    return $result;
  }

  public function getQuantityByToken($token)
  {
    $products = $this->getProducts();

    foreach ($products as $product)
    {
        if ($product['token']==$token) return $product['cart']['quantity'];
    }

    return 0;
  }

  public function getProducts()
  {
    $this->calculateDiscount();
    return !empty($this->products) ? $this->products : array();
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
    $this->loadProducts(true);

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
      $this->products = $productTable->createListByIds($productIds, array('index' => array('product' => 'id'), 'with_property' => false, 'view' => 'list', 'property_view' => false));
    }
    else
    {
      $currentIds = $this->products->getKeys();

      $toAddIds = array_diff($productIds, $currentIds);
      $toDelIds = array_diff($currentIds, $productIds);

      $toAdd = $productTable->createListByIds($toAddIds, array('index' => array('product' => 'id'), 'with_property' => false, 'view' => 'list', 'property_view' => false));
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
      $this->updateProductCart($product, 'quantity', $products[$key]['quantity']);
      $this->updateProductCart($product, 'formatted_total', number_format($products[$key]['quantity'] * $product->price, 0, ',', ' '));
      $this->updateProductCart($product, 'service', $products[$key]['service']);
    }
  }

  protected function updateProductCart(Doctrine_Record &$product, $property, $value)
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

