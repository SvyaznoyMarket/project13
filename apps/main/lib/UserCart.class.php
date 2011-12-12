<?php

class UserCart extends BaseUserData
{
  protected
    $parameterHolder = null,
    $products = null,
    $services = null
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


  public function addService(Service $service, $quantity = 1, $product = NULL)
  {
    if ($product) {
        $products = $this->parameterHolder->get('products');
        //если в корзине нет товара, к которому надо привязать услугу,
        //добавим этот товар в корзину
        if (!isset($products[$product->id])) {
            $this->addProduct($product, 1);
        }
    }

    $services = $this->parameterHolder->get('services');
    if (!isset($services[$service->id]) || empty($services[$service->id]))
    {
      $services[$service->id] = $this->getServiceDefaults();
    }
    if ($product) {
        //проверяем, можно ли добавлять эту услугу к этому продукту
        $mayToAdd = false;
        $avaleServiceList = ServiceTable::getInstance()->getListByProduct($product);
        foreach ($avaleServiceList as $nextService) {
            if ($nextService->id == $service->id) {
                $mayToAdd = true;
                break;
            }
        }
        if ($mayToAdd) {
            $services[$service->id]['product'][$product->id] = $quantity;
        } else {
            $services[$service->id]['quantity'] = $quantity;
        }

    } else {
        $services[$service->id]['quantity'] = $quantity;
    }
    $this->parameterHolder->set('services', $services);

    $this->calculateDiscount();
   # myDebug::dump( $this->services );
    return true;

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

  public function getService($id)
  {
    $services = $this->parameterHolder->get('services');
    $service = null;

    if (isset($services[$id]) && !empty($services[$id]))
    {
      $this->loadServices();
      $service = $this->services->get($id);
    }

    return $service;
  }

  public function deleteProduct($id)
  {

    $products = $this->parameterHolder->get('products');

    if (isset($products[$id]))
    {
      unset($products[$id]);
      $this->parameterHolder->set('products', $products);
      #$this->calculateDiscount();
    }

    if ($services = $this->parameterHolder->get('services'))
    {
      //удаляем из корзины сервисы, привязанные к этому товару
      foreach($services as & $service) {
          if (isset($service['product'][$id])) {
              unset($service['product'][$id]);
          }
      }
      #myDebug::dump($services);
      $this->parameterHolder->set('services', $services);
    }
    $this->calculateDiscount();


  }


  /** DEPRICATED
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
   *
   * */

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
    #myDebug::dump( $list );
    return $list;

  }

  public function getProductServiceList($getAllServices = false){

    $list = array();
    $productTable = ProductTable::getInstance();
    foreach ($this->getProducts() as $product)
    {
      $services = $product->getServiceList();
      $service_for_list = array();
      /*
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
       *
       */
            #print_r( $product['cart'] );

      $list[$product->id] = array(
        'type'      => 'product',
        'id'      => $product->id,
        'token'      => $product->token,
        'name'      => $product->name,
        'quantity'  => $product['cart']['quantity'],
        'service'   => $service_for_list,
        'product'   => $product,
        'price'     => $productTable->getRealPrice($product),
        'priceFormatted'     => $product->getFormattedPrice(),
        'total'     => $product['cart']['formatted_total'],
        'photo'     => $product->getMainPhotoUrl(1),
      );
    }
    #myDebug::dump($this->getServices());
    foreach ($this->getServices() as $service)
    {
        if (isset($service['cart']['product']) && count($service['cart']['product'])>0) {
            foreach($service['cart']['product'] as $product => $qty) {

                $list[$product]['service'][] = array(
                    'id'      => $service->id,
                    'token'     => $service->token,
                    'name'      => $service->name,
                    'quantity'  => $qty,
                    #'service'   => $service,
                    'price'     => $service->price,
                    'priceFormatted'     => $service->getFormattedPrice(),
                    'total'     => $service['cart']['formatted_total'],
                    #'photo'     => $service->getPhotoUrl(2),
                    );
            }
        }
        if ($service['cart']['quantity'] > 0) {
            #print_r( $service['cart'] );
            $list[$service->id] = array(
                'type'      => 'service',
                'id'        => $service->id,
                'token'     => $service->token,
                'name'      => $service->name,
                'quantity'  => $service['cart']['quantity'],
                'service'   => $service,
                'price'     => $service->price,
                'total'     => $service['cart']['formatted_total'],
                'priceFormatted'  => $service->getFormattedPrice(),
                'photo'     => $service->getPhotoUrl(2),
                );
        }
    }
    #myDebug::dump($list);
    return $list;
  }

  public function getServiceForProductQty(Service $service, $productId = 0)
  {
    $services = $this->parameterHolder->get('services');
    if ($productId) {
        if (isset($services[$service->id]) && isset($services[$service->id]['product'][$productId])) {
            return $services[$service->id]['product'][$productId];
        }
    } else {
        return $services[$service->id]['quantity'];
    }

  }
  public function deleteService(Service $service, $productId = 0)
  {
    $services = $this->parameterHolder->get('services');
    if (isset($services[$service->id]))
    {
      if ($productId) {
         # echo $productId.'--del';
         # exit();
        if (isset($services[$service->id]['product'][$productId])) {
            unset($services[$service->id]['product'][$productId]);
        }
      } else {
          $services[$service->id]['quantity'] = 0;
      }
      //если этого сервиса не осталось не по одиночке, не для товаров, удалим его вообще
      if (isset($services[$service->id]) && !count($services[$service->id]['product']) && !$services[$service->id]['quantity'] ) {
          unset( $services[$service->id] );
      }
      $this->parameterHolder->set('services', $services);
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

  /**
   * array('mode_id' => 'price')
   * @return array
   */
  public function getDeliveriesPrice()
  {
    $dProducts_raw = $this->getProducts();
    $dProducts = array();
    foreach ($dProducts_raw as $dProduct) {
      $dProducts[] = array('id' => $dProduct->core_id, 'quantity' => $dProduct->cart['quantity']);
    }
    $deliveries = Core::getInstance()->query('delivery.calc', array(), array(
      'geo_id' => sfContext::getInstance()->getUser()->getRegion('core_id'),
      'product' => $dProducts
    ));
    if (!$deliveries || !count($deliveries) || isset($deliveries['result'])) {
      $deliveries = array(array(
        'mode_id' => 1,
        'date' => date('Y-m-d', time()+(3600*48)),
        'price' => null,
      ));
    }
    $result = array();
    foreach ($deliveries as $d) {
      $deliveryObj = DeliveryTypeTable::getInstance()->findOneByCoreId($d['mode_id']);
      $result[$deliveryObj['id']] = $d['price'];
    }
    return $result;
  }

  public function getTotal($is_formatted = false)
  {
    $this->calculateDiscount();

    $total = 0;
    $products = $this->getProducts();
    $services = $this->getServices();

    foreach ($products as $product)
    {
      $total += ProductTable::getInstance()->getRealPrice($product) * $product['cart']['quantity'];
    }

    //$products = null;
    foreach ($services as $service)
    {
        $qty = $service['cart']['quantity'];
        if (isset($service['cart']['product'])) {
            foreach($service['cart']['product'] as $prodQty) {
                $qty += $prodQty;
            }
        }
        $total += ($service->getCurrentPrice() * $qty);
    }

    $result = $is_formatted ? number_format($total, 0, ',', ' ') : $total;


    return $result;
  }

  public function getReceiptList() {
    $total = 0;
    $products = $this->getProducts();
    $services = $this->getServices();
    #myDebug::dump($services);

    foreach ($products as $product)
    {
        $list[] = array(
            'type' => 'product',
            'name' => $product->name,
            'token' => $product->token,
            'quantity' => $product['cart']['quantity'],
            'price' => $product['cart']['formatted_total'],
            'photo' => $product->getMainPhotoUrl(1)
        );
    }

    //$products = null;
    foreach ($services as $service)
    {
        $qty = $service['cart']['quantity'];
        if (isset($service['cart']['product'])) {
            foreach($service['cart']['product'] as $prodQty) {
                $qty += $prodQty;
            }
        }
        $list[] = array(
            'type' => 'service',
            'name' => $service->name,
            'token' => $service->token,
            'quantity' => $qty,
            'price' => $service->getCurrentPrice() * $qty,
            'photo' => $service->getPhotoUrl(2)
        );

    }


    return $list;
  }


  public function getQuantityByToken($token)
  {
    $products = $this->getProducts();

    foreach ($products as $product)
    {
        if ($product['token_prefix'].'/'.$product['token']==$token) return $product['cart']['quantity'];
    }

    return 0;
  }

  public function getServiceQuantityByToken($token)
  {
    $services = $this->getServices();

    /*
    foreach ($services as $service)
    {
        if ($services['token']==$token) return $services['cart']['quantity'];
    }*/

    return 0;
  }

  public function getProducts()
  {
    $this->calculateDiscount();
    return !empty($this->products) ? $this->products : array();
  }

  public function getServices()
  {
    $this->calculateDiscount();
    return !empty($this->services) ? $this->services : array();
  }

  public function count()
  {
    $count = count($this->parameterHolder->get('products'));
    return $count;
  }

  public function countFull()
  {
    $count = count($this->parameterHolder->get('products')) + count($this->parameterHolder->get('services'));
    return $count;
  }

  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }

  protected function calculateDiscount()
  {
    $this->loadProducts(true);
    $this->loadServices(true);

    if ($this->products) {
        foreach ($this->products as $product)
        {
          $this->updateProductCart($product, 'discount', 0);
        }
    }
    if ($this->services) {
        foreach ($this->services as $service)
        {
          $this->updateServiceProductCart($service, 'discount', 0);
        }
    }
  }

  protected function loadProducts($force = false)
  {
    $products = $this->parameterHolder->get('products');
    $productIds = array_keys($products);
    $productTable = ProductTable::getInstance();

    if (is_null($this->products) || true === $force)
    {
      //myDebug::dump($productIds);
      $this->products = $productTable->createListByIds($productIds, array('index' => array('product' => 'id'), 'with_property' => false, 'view' => 'list', 'property_view' => false));
      //myDebug::dump($this->products);
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
      //myDebug::dump($product);
      $this->updateProductCart($product, 'quantity', $products[$key]['quantity']);
      $this->updateProductCart($product, 'formatted_total', number_format($products[$key]['quantity'] * ProductTable::getInstance()->getRealPrice($product), 0, ',', ' '));
      //myDebug::dump($product, 1);
      #$this->updateProductCart($product, 'service', $products[$key]['service']);
    }
  }

  protected function loadServices($force = false)
  {
    $services = $this->parameterHolder->get('services');
    $serviceIds = array();
    if ($services) {
        $serviceIds = array_keys($services);
    }
    $serviceTable = ServiceTable::getInstance();

    if (is_null($this->services) || true === $force)
    {
      $this->services = $serviceTable->createListByIds($serviceIds, array('index' => array('service' => 'id'), 'with_property' => false, 'view' => 'list', 'property_view' => false));
    }
    else
    {
      $currentIds = $this->services->getKeys();

      $toAddIds = array_diff($serviceIds, $currentIds);
      $toDelIds = array_diff($currentIds, $serviceIds);

      $toAdd = $serviceTable->createListByIds($toAddIds, array('index' => array('service' => 'id'), 'with_property' => false, 'view' => 'list', 'property_view' => false));
      foreach ($toAdd as $key => $service)
      {
        $this->services[$key] = $service;
      }

      foreach ($toDelIds as $id)
      {
        $this->services->remove($id);
      }
    }

    foreach ($this->services as $key => $service)
    {
      $this->updateServiceProductCart($service, 'quantity', $services[$key]['quantity']);
      $this->updateServiceProductCart($service, 'product', $services[$key]['product']);
      $this->updateServiceProductCart($service, 'formatted_total', number_format($services[$key]['quantity'] * $service->getCurrentPrice(), 0, ',', ' '));
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

  protected function updateServiceProductCart(Doctrine_Record &$service, $property, $value)
  {
    if (isset($service->cart))
    {
      $cart = $service->cart;
    }
    else
    {
      $cart = $this->getServiceDefaults();
    }
    $cart[$property] = $value;

    $service->mapValue('cart', $cart);
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
#      'service' => array(),
      'warranty' => array(),
    );
  }

  protected function getServiceDefaults()
  {
    return array(
      'quantity' => 0,
      'discount' => 0,
      'product' => array(),
      'warranty' => array(),
    );
  }
}

