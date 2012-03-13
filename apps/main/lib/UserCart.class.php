<?php

class UserCart extends BaseUserData
{

  public
    $attributeHolder,
    $_products = array(),
    $_services = array()

  ;

  function __construct($parameters = array())
  {
      $cart = sfContext::getInstance()->getUser()->getAttribute('cartSoa', array());
      if (isset($cart['products'])) {
          $this->_products = $cart['products'];
      }
      if (isset($cart['services'])) {
          $this->_services = $cart['services'];
      }
  }

  private function _save() {
      $cart = array(
          'products' => $this->_products,
          'services' => $this->_services,
      );
      sfContext::getInstance()->getUser()->setAttribute('cartSoa', $cart);
  }

  public function addProduct($id, $qty = 1, $isKit = false)
  {
        if ($isKit)
        {
            //TODO комплекты
            $products = array('1', '2', '3');
        }
        else
        {
            $products = array($id);
        }

        try
        {
            //загружаем объект из ядра, чтоб узнать его цену
            $factory = new ProductFactory();
            $productOb = $factory->createProductFromCore(array('id' => $id), true);

            foreach ($products as $product)
            {
                if ($qty <= 0)
                {
                    $qty = 0;
                    $this->deleteProduct($product);
                }
                else
                {
                    $this->_products[$product] = array(
                        'id' => $product,
                        'quantity' => $qty,
                        'price' => $productOb->price,
                       // 'priceFormatted' => number_format($priceAr[$product]['price'], 0, ',', ' '),
                       // 'total' => number_format($priceAr[$product]['price'] * $qty, 0, ',', ' ')
                    );
                }
            }
        }
        catch (Exception $e)
        {
            $result['value'] = false;
            $result['error'] = "Не удалось добавить в корзину товар token='".$id."'.";
            return false;
        }
        $this->_save();
        return true;
  }
  
  
  private function _addProductItself($id, $quantity = 1)
  {
      //убиваю первый товар в корзине, если размер превышает 5000
//      if (count($products) >= 5000)
//      {
//        $keys = array_keys($products);
//        unset($products[$keys[0]]);
//      }

    $this->_products[$id] = array(
        'id' => $id,
        'quantity' => $quantity
        );
  }

  public function addService($serviceId, $quantity = 1, $productId = 0)
  {
    if ($productId)
    {
      //если в корзине нет товара, к которому надо привязать услугу,
      //добавим этот товар в корзину
      if (!isset($this->_products[$productId]))
      {
        $this->addProduct($productId, 1);
      }
    }
    //получаем цену на услугу. пока из БД!
    $region = sfContext::getInstance()->getUser()->getRegion();
    $priceList = $region['product_price_list_id'];
    $serviceInfo = ServiceTable::getInstance()->findOneBy('core_id', $serviceId);
    $priceData = ServicePriceTable::getInstance()->getQueryObject()
      ->addWhere('service_id = ?', $serviceInfo->id)
      ->addWhere('service_price_list_id = ?', $priceList)
      ->addWhere('product_id = ? OR product_id IS NULL', $productId)
      ->fetchArray()
    ;
//      print_r($priceData);
//      die();
    foreach ($priceData as $k => $info) {
        if ($info['product_id'] == $productId) {
            $priceVal = $info['price'];
            break;
        } elseif (!$info['product_id']) {
            $priceVal = $info['price'];
        }
    }


    if (!isset($this->_services[$serviceId]) || empty($this->_services[$serviceId]))
    {
      $this->_services[$serviceId] = $this->getServiceDefaults();
    }
    if ($productId)
    {
      //проверяем, можно ли добавлять эту услугу к этому продукту
      $mayToAdd = false;
//      $avaleServiceList = ServiceTable::getInstance()->getListByProduct($product);
//      foreach ($avaleServiceList as $nextService)
//      {
//        if ($nextService->id == $service->id)
//        {
//          $mayToAdd = true;
//          break;
//        }
//      }
      $mayToAdd = true;
      if ($mayToAdd)
      {
        $isInCart = false;
        foreach($this->_products as $inCartProductId => $info) {
           if ($inCartProductId == $productId) {
               $isInCart = true;
           }
        }
        //товар, к которому привязываем должен либо находиться в корзине,
        //либо являться комплектом
        if ($isInCart) { // || $product->isKit()) {
            $this->_services[$serviceId]['products'][$productId] = array(
                'quantity' => $quantity,
                'price' => $priceVal
                );
        } else {
            $this->_services[$serviceId]['products'][0] = array(
                'quantity' => $quantity,
                'price' => $priceVal
            );
        }
      } else {
          $this->_services[$serviceId]['products'][0] = array(
              'quantity' => $quantity,
              'price' => $priceVal
          );
      }
    } else {
      $this->_services[$serviceId]['products'][0] = array(
          'quantity' => $quantity,
          'price' => $priceVal
      );
    }
    $this->_services[$serviceId]['id'] = $serviceId;

    $this->_save();
    return true;
  }

  public function getProduct($id)
  {
    if (isset($this->_products[$id])) {
        return $this->_products[$id];
    }
  }

//    public function getProductByCoreId($id)
//    {
//        return null;
//    }

  public function getService($id)
  {
      return $this->_services[$id];
  }

  public function deleteProduct($id)
  {
    if (isset($this->_products[$id])) {
        unset($this->_products[$id]);
    }
    foreach ($this->_services as & $service) {
        foreach ($service['products'] as $prodId => $servProdInfo) {
            if ($prodId == $id) {
                $this->deleteService($service['id'], $id);
            }
        }
    }
    $this->_save();
  }


  public function getServicesByProductId($productId)
  {
    $serviceList = array();
    foreach ($this->_services as $service) {
        foreach ($service['products'] as $prodInfo) {
            if ($productId = $prodInfo['id']) {
               $serviceList[] =  $service;
               break;
            }
        }
    }

    return $serviceList;
  }

  public function getProductServiceList($getAllServices = false)
  {

    $list = array();
    $region = sfContext::getInstance()->getUser()->getRegion();
    $priceList = $region['product_price_list_id'];
    $productIdList = array();
    $serviceIdList = array();
    foreach ($this->_products as $product) {
        $productIdList[] = $product['id'];
    }
    foreach ($this->_services as $service) {
        $serviceIdList[] = $service['id'];
    }
    $productTable = ProductTable::getInstance();
    $serviceTable = ServiceTable::getInstance();
    $productBDList = $productTable->getQueryObject()
        ->whereIn('core_id', $productIdList)
        ->fetchArray();
      //myDebug::dump($productBDList);
    foreach ($productBDList as $k => $pr) {
        unset($productBDList[$k]);
        $productBDList[$pr['core_id']] = $pr;
    }
    $serviceBDList = $serviceTable->getQueryObject()->whereIn('core_id', $serviceIdList)->fetchArray();
    foreach ($serviceBDList as $k => $pr) {
        unset($serviceBDList[$k]);
        $serviceBDList[$pr['core_id']] = $pr;
    }
     // myDebug::dump($productBDList);
//      myDebug::dump($serviceBDList);

    //die();
    $urls = sfConfig::get('app_product_photo_url');
    foreach ($this->_products as $product)
    {
      $prodId = $product['id'];
      $service_for_list = array();
      $list[$prodId] = array(
        'type' => 'product',
        'id' => $product['id'],
        'core_id' => $productBDList[$prodId]['core_id'],
        'token_prefix' => $productBDList[$prodId]['token_prefix'],
        'token' => $productBDList[$prodId]['token'],
        'name' => $productBDList[$prodId]['name'],
        'quantity' => $product['quantity'],
        'service' => $service_for_list,
        'price' => $product['price'],
        'priceFormatted' =>  number_format($product['price'], 0, ',', ' '),
        'total' => number_format($product['price'] * $product['quantity'], 0, ',', ' '),
        'photo' => $urls[1] . $productBDList[$prodId]['main_photo'],
      );
    }
    #myDebug::dump($this->getServices());
    foreach ($this->_services as $service)
    {
      $serviceId = $service['id'];
      if (isset($service['products']) && count($service['products']) > 0)
      {
        foreach ($service['products'] as $product => $serviceProductData)
        {

          if ($product == 0) {
              #print_r( $service['cart'] );
              $list[$serviceId] = array(
                  'type' => 'service',
                  'id' => $serviceId,
                  'core_id' => $serviceBDList[$serviceId]['core_id'],
                  'token' => $serviceBDList[$serviceId]['token'],
                  'name' =>  $serviceBDList[$serviceId]['name'],
                  'quantity' => $serviceProductData['quantity'],
                  'service' => $service,
                  'price' => $serviceProductData['price'],
                  'total' => number_format($serviceProductData['price'] * $serviceProductData['quantity'], 0, ',', ' '),
                  'priceFormatted' => number_format($serviceProductData['price'], 0, ',', ' '),
                  'photo' => $urls[2] . $serviceBDList[$serviceId]['main_photo'],
              );
          }  elseif (isset($list[$product])) {
            $list[$product]['service'][] = array(
                'id' => $serviceId,
                'core_id' => $serviceBDList[$serviceId]['core_id'],
                'token' => $serviceBDList[$serviceId]['token'],
                'name' => $serviceBDList[$serviceId]['name'],
                'quantity' => $serviceProductData['quantity'],
                'price' => $serviceProductData['price'],
                'priceFormatted' => number_format($serviceProductData['price'], 0, ',', ' '),
                'total' => number_format($serviceProductData['price'] * $serviceProductData['quantity'], 0, ',', ' '),
            );
          } else {
                $productOb = ProductTable::getInstance()->getById($product) ;
                $list[$service->id] = array(
                'type' => 'service',
                'id' => $serviceId,
                'core_id' => $serviceBDList[$serviceId]['core_id'],
                'token' => $serviceBDList[$serviceId]['token'],
                'name' => $serviceBDList[$serviceId]['name'],
                'quantity' => $serviceProductData['quantity'],
                'service' => $service,
                'price' => $serviceProductData['price'],
                'total' => number_format($serviceProductData['price'] * $serviceProductData['quantity'], 0, ',', ' '),
                'priceFormatted' => number_format($serviceProductData['price'], 0, ',', ' '),
                'photo' => $urls[2] . $serviceBDList[$serviceId]['main_photo'],
                'products' => $serviceBDList[$serviceId]['token_prefix'] . '/' . $serviceBDList[$serviceId]['token']
                );
          }
        }
      }

    }
      //myDebug::dump($list);

    return $list;
  }

//  public function getServiceForProductQty(Service $serviceId, $productId = null)
//  {
//      if (isset($this->_services[$serviceId]) && isset($this->_services[$serviceId]['products'][$productId]))
//      {
//        return $this->_services[$serviceId]['products'][$productId]['quantity'];
//      }
//      return 0;
//  }



  public function deleteService($id, $productId = 0)
  {
      if (isset($this->_services[$id]) && isset($this->_services[$id]['products'][$productId])) {
          unset($this->_services[$id]['products'][$productId]);
      }
      if (!isset($this->_services[$id]['products']) || !count($this->_services[$id]['products'])) {
          unset($this->_services[$id]);
      }
      $this->_save();
  }

  public function clear()
  {
    if (count($this->_products)) {
      $this->_products = array();
    }
    if (count($this->_services)) {
      $this->_services = array();
    }
  }

  public function hasProduct($id)
  {
     if (isset($this->_products[$id])) {
         return true;
     }
     return false;
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
//    $dProducts_raw = $this->getProducts();
//    $dProducts = array();
//    foreach ($dProducts_raw as $dProduct)
//    {
//      $dProducts[] = array('id' => $dProduct->core_id, 'quantity' => $dProduct->cart['quantity']);
//    }
//    $deliveries = Core::getInstance()->query('delivery.calc', array(), array(
//      'geo_id' => sfContext::getInstance()->getUser()->getRegion('core_id'),
//      'products' => $dProducts
//      ));
//    if (!$deliveries || !count($deliveries) || isset($deliveries['result']))
//    {
//      $deliveries = array(array(
//        'mode_id' => 1,
//        'date' => date('Y-m-d', time() + (3600 * 48)),
//        'price' => null,
//      ));
//    }
//    $result = array();
//    foreach ($deliveries as $d)
//    {
//      $deliveryObj = DeliveryTypeTable::getInstance()->getByCoreId($d['mode_id']);
//      $result[$deliveryObj['id']] = $d['price'];
//    }
    $result = array();
    return $result;
  }

  public function getTotal($is_formatted = false)
  {
    $total = 0;
    foreach ($this->_products as $product)
    {
      $total += $product['price'] * $product['quantity'];
    }

    foreach ($this->_services as $service)
    {
      if (isset($service['products']))
      {
        foreach ($service['products'] as $prodToken => $prodQty)
        {
          $total += ($prodQty['price'] * $prodQty['quantity']);
        }
      }
    }

    $result = $is_formatted ? number_format($total, 0, ',', ' ') : $total;


    return $result;
  }

  public function getReceiptList()
  {
//    $total = 0;
//    $products = $this->getProducts();
//    $services = $this->getServices();
//    #myDebug::dump($services);
//
//    foreach ($products as $product)
//    {
//      $list[] = array(
//        'type' => 'products',
//        'name' => $product->name,
//        'token' => $product->token,
//        'token_prefix' => $product->token_prefix,
//        'quantity' => $product['cart']['quantity'],
//        'price' => $product['cart']['formatted_total'],
//        'photo' => $product->getMainPhotoUrl(1)
//      );
//    }
//
//    //$products = null;
//    foreach ($services as $service)
//    {
//      $qty = $service['cart']['quantity'];
//      $price = $service->getCurrentPrice() * $qty;
//      if (isset($service['cart']['products']))
//      {
//        foreach ($service['cart']['products'] as $prodId => $prodQty)
//        {
//          $qty += $prodQty;
//          $price += $service->getCurrentPrice($prodId) * $prodQty;
//        }
//      }
//      $list[] = array(
//        'type' => 'service',
//        'name' => $service->name,
//        'token' => $service->token,
//        'quantity' => $qty,
//        'price' => number_format($price, 0, ',', ' '),
//        'photo' => $service->getPhotoUrl(2)
//      );
//    }

    $list = array();
    return $list;
  }

  public function getQuantityById($id)
  {
    if (isset($this->_products[$id])) {
        return $this->_products[$id]['quantity'];
    }
    return 0;
  }

  public function getServiceQuantityById($id, $productId = 0)
  {
     if (!isset($this->_services[$id])) {
         return 0;
     }
     if ($productId == 'all') {
         $qty = 0;
         foreach ($this->_services[$id]['products'] as $prodQty) {
             $qty += $prodQty;
         }
     } else {
         $qty = $this->_services[$id]['products'][$productId]['quantity'];
     }
     return $qty;
  }

  public function getProducts()
  {
    return !empty($this->_products) ? $this->_products : array();
  }

  public function getServices()
  {
    return !empty($this->_services) ? $this->_services : array();
  }

  public function count()
  {
    $count = 0;
    foreach ($this->_products as $product) {
      $count += $product['quantity'];
    }

    return $count;
  }

  public function countFull()
  {
    return count($this->_products) + count($this->_services);
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
    if (isset($this->_services[$id])) {
        return $this->_services[$id];
    }
  }


  protected function getDefaults()
  {
    return array(
      //'discount' => 0,
      //'warranty' => array(),
    );
  }

  protected function getServiceDefaults()
  {
    return array(
      //'quantity' => 0,
      //'discount' => 0,
      'products' => array(),
      //'warranty' => array(),
    );
  }


    public function getBaseInfo()
    {
        $result['qty'] = $this->count();
        $result['sum'] = $this->getTotal();
        $result['productsInCart'] = array();
        $result['servicesInCart'] = array();

        foreach ($this->_products as $id => $product)
        {
            $result['productsInCart'][$id] = $product['quantity'];
        }

        foreach ($this->_services as $id => $service)
        {

            foreach ($service['products'] as $pId => $pQty)
            {
                $result['servicesInCart'][$id][$pId] = $pQty;
            }
        }

        return $result;
    }

    public function getSeoCartArticle()
    {
        $orderArticleAR = array();
        foreach ($this->getProducts() as $product) {
            $orderArticleAR[] = $product->barcode;
        }
        $orderArticle = implode(',', $orderArticleAR);
        return $orderArticle;

    }

}

