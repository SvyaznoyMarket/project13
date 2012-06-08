<?php

class UserCart extends BaseUserData
{

    public
        $parameterHolder,
        $attributeHolder,
        $_products = array(),
        $_services = array()

    ;

    function __construct($parameters = array())
    {
	      //заглядываем в старую карзину
	      $this->_useOldCart();
//        sfContext::getInstance()->getUser()->setAttribute('cartSoa', array());
//        return;
        $cart = sfContext::getInstance()->getUser()->getAttribute('cartSoa', array());
        if (isset($cart['products'])) {
            $this->_products = $cart['products'];
        }
        if (isset($cart['services'])) {
            $this->_services = $cart['services'];
        }
        $parameters = myToolkit::arrayDeepMerge(array('products' => array(),), $parameters);
        $this->parameterHolder = new sfParameterHolder();
        $this->parameterHolder->add($parameters);
    }

    /**
     * если у пользователя сохранилась корзина старого образца, переписываем её на новую, а про старое забываем.
     */
    private function _useOldCart()
    {
        $cartOld = sfContext::getInstance()->getUser()->getAttribute('cart', array());
        if (isset($cartOld['products'])) {
            foreach ($cartOld['products'] as $oldCartItemId => $oldCartItem) {
                $prodDbOb = ProductTable::getInstance()->createBaseQuery()->where('id = ?', $oldCartItemId)->fetchOne();
                $this->addProduct($prodDbOb->core_id, $oldCartItem['quantity']);
            }
        }
        if (isset($cartOld['services'])) {
            foreach ($cartOld['services'] as $oldCartServiceId => $oldCartService) {
                $serviceDbOb = ServiceTable::getInstance()->createBaseQuery()->where('id = ?', $oldCartServiceId)->fetchOne();
                if (isset($oldCartService['quantity']) && $oldCartService['quantity'] > 0) {
                    $this->addService($serviceDbOb->core_id, $oldCartService['quantity'], 0);
                }
                if (isset($oldCartService['product'])) {
                    foreach ($oldCartService['product'] as $prodId => $prodQty) {
                        $this->addService($serviceDbOb->core_id, $prodQty, $prodId);
                    }
                }
            }
        }
        sfContext::getInstance()->getUser()->setAttribute('cart', array());
    }

    private function _save() {
        $cart = array(
            'products' => $this->_products,
            'services' => $this->_services,
        );
        sfContext::getInstance()->getUser()->setAttribute('cartSoa', $cart);
    }

    public function addProduct($id, $qty = 1)
    {
        //получаем информацию о продукте из ядра
        $productList = RepositoryManager::getProduct()->getListById(array($id), true);
        /** @var $product ProductEntity */
        $product = reset($productList);

        $isKit = false;
        $kitQtyByIdList = array();
        if ($product->getSetId() == 2)
        {
            $isKit = true;
            //загружаем инфу о составе комплекта
            $kitIdList = array();
            foreach ($product->getKitList() as $kit) {
                $kitIdList[] = $kit->getProductId();
                $kitQtyByIdList[$kit->getProductId()] = $kit->getQuantity();
            }
            $productList = RepositoryManager::getProduct()->getListById($kitIdList, true);
        }

        try
        {
            foreach ($productList as $product)
            {
                if ($qty <= 0)
                {
                    $qty = 0;
                    $this->deleteProduct($product->getId());
                }
                else
                {
                    if ($isKit) {
                        //нужное количество умножаем на количество предметов в комплекте
                        $addQty = $qty * $kitQtyByIdList[$product->getId()];
                    } else {
                        $addQty = $qty;
                    }
                    $this->_products[$product->getId()] = array(
                        'id' => $product->getId(),
                        'token' => $product->getToken(),
                        'quantity' => $addQty,
                        'price' => $product->getPrice(),
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
        $prodOb = ProductTable::getInstance()->createBaseQuery()->where('core_id = ?', $productId)->fetchOne();
        $siteProductId = $prodOb->id;

        //получаем цену на услугу. пока из БД! пока нет API для услуг! передалать!
        $region = sfContext::getInstance()->getUser()->getRegion();
        $priceList = $region['product_price_list_id'];
        $serviceInfo = ServiceTable::getInstance()->findOneBy('core_id', $serviceId);
        $priceData = ServicePriceTable::getInstance()->getQueryObject()
            ->addWhere('service_id = ?', $serviceInfo->id)
            ->addWhere('service_price_list_id = ?', $priceList)
            ->addWhere('product_id = ? OR product_id IS NULL', $siteProductId)
            ->fetchArray()
        ;
        //      print_r($priceData);
        //      die();
        foreach ($priceData as $k => $info) {
            if ($info['product_id'] == $siteProductId) {
                $priceVal = $info['price'];
                break;
            } elseif (!$info['product_id']) {
                $priceVal = $info['price'];
            }
        }

        $serviceCartInfo = array(
            'quantity' => $quantity,
            'price' => $priceVal
        );

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
                    $this->_services[$serviceId]['products'][$productId] = $serviceCartInfo;
                } else {
                    $this->_services[$serviceId]['products'][0] = $serviceCartInfo;
                }
            } else {
                $this->_services[$serviceId]['products'][0] = $serviceCartInfo;
            }
        } else {
            $this->_services[$serviceId]['products'][0] = $serviceCartInfo;
        }
        $this->_services[$serviceId]['id'] = $serviceId;
        $this->_services[$serviceId]['token'] = $serviceInfo['token'];


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
        $urlsService = sfConfig::get('app_service_photo_url');
        foreach ($this->_products as $product)
        {
            $prodId = $product['id'];
            if (!isset($productBDList[$prodId]))
            {
              mail('ssv@enter.ru, pavel.kuznetsov@enter.ru, ssv@enter.ru, georgiy.lazukin@enter.ru', 'Missing product',
                'Someone is trying to buy product #'.$prodId.' but we dont have it! Here you are: '."\n"
                .'session: '.session_id()."\r\n"
                .'date: '.date('Y-m-d H:i:s')."\r\n"
                .'cart: '.sfYaml::dump($this->dump())."\r\n"
              );
              continue;
            }
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
                            'photo' => $urlsService[2] . $serviceBDList[$serviceId]['main_photo'],
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
                            'photo' => $urlsService[2] . $serviceBDList[$serviceId]['main_photo'],
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
        $this->_save();
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
        $dProducts = array();
        foreach ($this->_products as $dProduct)
        {
            $dProducts[] = array(
                'id' => $dProduct['id'],
                'quantity' => $dProduct['quantity']
            );
        }
        $deliveries = Core::getInstance()->query('delivery.calc', array(), array(
            'geo_id' => sfContext::getInstance()->getUser()->getRegion('core_id'),
            'product' => $dProducts
        ));
        if (!$deliveries || !count($deliveries) || isset($deliveries['result']))
        {
            $deliveries = array(array(
                'mode_id' => 1,
                'date' => date('Y-m-d', time() + (3600 * 48)),
                'price' => null,
            ));
        }
        $result = array();
        foreach ($deliveries as $d)
        {
            $deliveryObj = DeliveryTypeTable::getInstance()->getByCoreId($d['mode_id']);
            $result[$deliveryObj['id']] = $d['price'];
        }
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
        foreach ($service['products'] as $prodId => $prodQty)
        {
          $total += ($prodQty['price'] * $prodQty['quantity']);
        }
      }
    }

    $result = $is_formatted ? number_format($total, 0, ',', ' ') : $total;


    return $result;
  }

  public function getTotalForOrder(Order $order)
  {
    $this->calculateDiscount();

    $total = 0;
    $products = $this->getProducts();
    $services = $this->getServices();

    $needleProductIds = array_map(function($i) { return $i->Product->id; }, iterator_to_array($order->ProductRelation));
    $needleServiceIds = array_map(function($i) { return $i->Service->id; }, iterator_to_array($order->ServiceRelation));

    foreach ($products as $product)
    {
      if (!in_array($product->id, $needleProductIds)) continue;

      $total += ProductTable::getInstance()->getRealPrice($product) * $product['cart']['quantity'];
    }

    //$products = null;
    foreach ($services as $service)
    {
      $qty = $service['cart']['quantity'];
      if ($qty) {
        if (!in_array($service->id, $needleServiceIds)) continue;

        $total += ($service->getCurrentPrice() * $qty);
      }

      if (isset($service['cart']['product']))
      {
        foreach ($service['cart']['product'] as $prodId => $prodQty)
        {
          if (!in_array($prodId, $needleProductIds)) continue;
          //$qty += $prodQty;
          $total += ($service->getCurrentPrice($prodId) * $prodQty);
        }
      }
    }


    return $total;
  }

    public function getReceiptList()
    {
        $prodIdList = array();
        foreach ($this->_products as $productId =>  $productInfo)
        {
            $productOb = ProductTable::getInstance()->getQueryObject()->where('core_id = ?', $productId)->fetchOne();
            $list[] = array(
                'type' => 'products',
                'name' => $productOb->name,
                'token' => $productOb->token,
                'token_prefix' => $productOb->token_prefix,
                'quantity' => $productInfo['quantity'],
                'price' => number_format($productInfo['price'], 0, ',', ' '),
            );
        }

        foreach ($this->_services as $serviceId => $serviceInfo)
        {
            $serviceOb = ServiceTable::getInstance()->getQueryObject()->where('core_id = ?', $serviceId)->fetchOne();

            $qty = 0;
            $price = 0;
            foreach ($serviceInfo['products'] as $prodId => $prodServInfo)
            {
                $qty += $prodServInfo['quantity'];
                $price += $prodServInfo['price'] * $prodServInfo['quantity'];
            }
            $list[] = array(
                'type' => 'service',
                'name' => $serviceOb->name,
                'token' => $serviceOb->token,
                'quantity' => $qty,
                'price' => number_format($price, 0, ',', ' '),
            );
        }

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



    protected function getServiceById($id)
    {
        if (isset($this->_services[$id])) {
            return $this->_services[$id];
        }
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
        foreach ($this->getProducts() as $productId =>  $product) {
            $orderArticleAR[] = $productId;
        }
        $orderArticle = implode(',', $orderArticleAR);
        return $orderArticle;

    }

}
