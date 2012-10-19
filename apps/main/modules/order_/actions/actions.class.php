<?php

/**
 * order_ actions.
 *
 * @package    enter
 * @subpackage order_
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class order_Actions extends myActions
{
  const ORDER_COOKIE_NAME = 'last_order';

  public function preExecute()
  {
    $this->getRequest()->setParameter('_template', 'order');
    $this->getResponse()->setTitle('Оформление заказа');
  }

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }

  /**
   * Executes new action
   *
   * @param sfRequest $request A request object
   */
  public function executeNew(sfWebRequest $request)
  {
    /* @var myUser */
    $user = $this->getUser();

    if (!$user->getCart()->count())
    {
      $this->redirect('cart');
    }

    $order = new Order();
    if(sfContext::getInstance()->getRequest()->getCookie('scId', false)){
      $order->setSclubCardNumber(sfContext::getInstance()->getRequest()->getCookie('scId'));
    }

    $this->order = $order;
    //$this->order->region_id = $this->getUser()->getRegion('id');

    // вытащить из куки значения для формы, если пользователь неавторизован
    if ($user->isAuthenticated()) {
      $this->order->recipient_first_name = $user->getGuardUser()->getFirstName();
      $this->order->recipient_last_name = $user->getGuardUser()->getLastName();
      $this->order->recipient_phonenumbers = $user->getGuardUser()->getPhonenumber();
    }
    else {
      $cookieValue = $request->getCookie(self::ORDER_COOKIE_NAME);
      if (!empty($cookieValue))
      {
        $cookieValue = (array)unserialize(base64_decode($cookieValue));
        foreach ($this->getCookieKeys() as $k)
        {
          if (array_key_exists($k, $cookieValue) && empty($this->order->{$k}))
          {
            $this->order->{$k} = $cookieValue[$k];
          }
        }
      }
    }

    $this->form = $this->getOrderForm($this->order);
    if (!$this->form)
    {
      $this->getRequest()->setParameter('_template', 'order_error');
      $this->setTemplate('error');

      return sfView::SUCCESS;
    }

    $deliveryTypes = $this->form->getOption('deliveryTypes');
    $defaultDeliveryType = (1 == count($deliveryTypes)) ? $deliveryTypes[0] : null;
    $deliveryMap = $this->getDeliveryMapView($defaultDeliveryType);

    $this->setVar('deliveryMap', $deliveryMap);
    $this->setVar('deliveryMap_json', json_encode($deliveryMap));
    $this->setVar('mapCenter', json_encode(array('latitude' => $user->getRegion('latitude'), 'longitude' => $user->getRegion('longitude'))));

    // получение ссылки "Вернуться к покупкам"
    $this->backLink = $this->generateUrl('cart');

    $productList = $user->getCart()->getProducts();

    if(!count($productList)){
      return;
    }

    $product = array_pop($productList);
    /** @var $product \light\ProductCartData */

    $products = Core::getInstance()->query('product.get', array('id' => $product->getProductId(), 'expand' => array('category')));
    if (!empty($products[0]['category'][0]['link']))
    {
      $this->backLink = $products[0]['category'][0]['link'];
    }
  }

  /**
   * Executes deliveryMap action
   *
   * @param sfRequest $request A request object
   */
  public function executeDeliveryMap(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    /* @var myUser */
    $user = $this->getUser();

    $deliveryType = $request['delivery_type_id'] ? DeliveryTypeTable::getInstance()->getById($request['delivery_type_id']) : null;
    $this->forward404Unless($deliveryType);

    $shop = $request['shop_id'] ? ShopTable::getInstance()->getById($request['shop_id']) : null;

    $deliveryMap = $this->getDeliveryMapView($deliveryType, $shop);

    return $this->renderJson(array(
      'success' => true,
      'data'    => $deliveryMap,
    ));
  }

  /**
   * Executes create action
   *
   * @param sfRequest $request A request object
   */
  public function executeCreate(sfWebRequest $request)
  {
    /* @var myUser */
    $user = $this->getUser();

    $result = array('success' => false);

    /* @var $form BaseForm */
    $form = $this->getOrderForm($this->order);
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      // если пользователь неавторизован, забросить его данные в куки
      if (!$user->isAuthenticated())
      {
        try {
          $values = $form->getValues();
          $coockieValue = array();
          foreach ($this->getCookieKeys() as $k) {
            if (!array_key_exists($k, $values)) continue;

            $coockieValue[$k] = $values[$k];
          }

          $this->getResponse()->setCookie(self::ORDER_COOKIE_NAME, base64_encode(serialize($coockieValue)), time() + (3600 * 24 * 30));
        }
        catch (Exception $e) {
          $this->getLogger()->err('{'.__CLASS__.'} не могу запихнуть куку: '.$e->getMessage());
        }
      }

      /* @var $baseOrder Order */
      $baseOrder = $form->updateObject();

      $baseOrder->mapValue('credit_bank_id', $form->getValue('credit_bank_id'));
      /*
      $baseOrder->address = ''
        .($form->getValue('address_metro') ? "метро {$form->getValue('address_metro')}, " : '')
        .("улица {$form->getValue('address_street')}, ")
        .("дом {$form->getValue('address_number')}")
        .($form->getValue('address_building') ? ", корпус/строение {$form->getValue('address_building')}" : '')
        .($form->getValue('address_apartment') ? ", квартира {$form->getValue('address_apartment')}" : '');
      */

      $deliveryMap = json_decode($request['delivery_map'], true);
      try {
        $orders = $this->saveOrder($baseOrder, $deliveryMap);
        $user->setFlash('complete_orders', array_map(function($i) { return $i['id']; }, $orders));

        $this->getUser()->setCacheCookie();
        $this->getUser()->getCart()->clear();
        $this->getUser()->getOrder()->clear(); //FIXME: убрать

        $result = array_merge($result, array(
          'success' => true,
          'data'    => array('redirect' => $this->generateUrl('order_complete')),
        ));
      }
      catch (Exception $e) {
        $result = array_merge($result, array(
          'success' => false,
          'error'   => array(
            'code'    => 'create',
            'message' => sfConfig::get('sf_web_debug') ? $e->getMessage() : 'При создании заказа возникли проблемы',
          ),
        ));
      }
    }
    else {
      $errors = array();
      foreach($form->getWidgetSchema()->getPositions() as $widgetName)
      {
        if ($form[$widgetName]->hasError())
        {
          $errors[$form[$widgetName]->renderName()] = $form[$widgetName]->getError()->getMessageFormat();
        }
      }

      $result = array_merge($result, array(
        'success' => false,
        'error'   => array(
          'code'    => 'invalid',
          'message' => 'Форма заполнена неверно',
        ),
        'errors'  => $errors,
      ));
    }

    return $this->renderJson($result);
  }

  /**
   * Executes createExternal action
   *
   * @param sfRequest $request A request object
   */
  public function executeCreateExternal(sfWebRequest $request)
  {
    /* @var myUser */
    $user = $this->getUser();

    $productInCart = $request['items'];
    $regionId = (int) $request['city_id'];

    $this->forward404Unless(is_array($productInCart) && count($productInCart));

    $this->getUser()->setRegion($regionId);

    $user->getCart()->clear();
    foreach ($productInCart as $id => $quantity)
    {
      $user->getCart()->addProduct($id, $quantity);
    }

    $params = array();
    foreach ($request->getParameterHolder()->getAll() as $k => $v)
    {
      if (0 === strpos($k, 'utm_'))
      {
        $params[$k] = $v;
      }
    }

    return $this->redirect($this->generateUrl('order_new').'?'.http_build_query($params));
  }

  /**
   * Executes complete action
   *
   * @param sfRequest $request A request object
   */
  public function executeComplete(sfWebRequest $request)
  {
    $request->setParameter('_template', 'order_complete');

    $this->paymentProvider = $this->getPaymentProvider();

    /* @var myUser */
    $user = $this->getUser();

    $orderIds = $user->getFlash('complete_orders');

    // проверяет наличие параметра от uniteller
    $orderNumber = $this->paymentProvider->getOrderIdFromRequest($request);
    if (!empty($orderNumber))
    {
      $result = Core::getInstance()->query('order.get', array(
        'number' => $orderNumber,
        'expand' => array('geo', 'user', 'product', 'service'),
      ));

      $orderIds = is_array($result) ? array_map(function($i) { return $i['id']; }, $result) : null;
    }
    else {
      $result = Core::getInstance()->query('order.get', array(
        'id'     => $orderIds,
        'expand' => array('geo', 'user', 'product', 'service', 'credit'),
      ));
    }

    if (empty($orderIds))
    {
    //  $this->redirect('cart');
    }

    $user->setFlash('complete_orders', $orderIds);

    //myDebug::dump($result);
    if (!$result)
    {
      $this->getLogger()->err('{Order} get list: empty response from core');
    }
    $orders = $result;

    // Инвойсинг
    if (8 == $orders[0]['payment_id']) {
      $this->paymentProvider = $this->getPaymentProvider('psbank_invoice');
    }

    $gaItems = array();

    foreach ($orders as &$order)
    {
      $gaItems[$order['number']] = array();

      // добавить названия товаров {
      if (isset($order['product']) && is_array($order['product']))
      {
        $products = RepositoryManager::getProduct()->getListById(array_map(function($i) {
          return $i['product_id'];
        }, $order['product']), true);

        $productsById = array();
        foreach ($products as $product)
        {
          /* @var $product ProductEntity */
          $productsById[$product->getId()] = $product;
        }

        foreach ($order['product'] as &$productData)
        {
          if (!array_key_exists($productData['product_id'], $productsById)) continue;

          $productData['name'] = $productsById[$productData['product_id']]->getName();

          $gaItem = new Order_GaItem();
          $gaItem->orderNumber = $order['number'];
          $gaItem->article = $productsById[$productData['product_id']]->getArticle();
          $gaItem->name = $productsById[$productData['product_id']]->getName();
          $gaItem->price = $productData['price'];
          $gaItem->quantity = $productData['quantity'];

          $categories = $productsById[$productData['product_id']]->getCategoryList();
          if (!empty($categories[0]) && ($categories[0] instanceof ProductCategoryEntity)) {
            $category = array_pop($categories);
            $rootCategory = array_shift($categories);

            $gaItem->categoryName =
              ($rootCategory && ($rootCategory->getId() != $category->getId()))
              ? ($rootCategory->getName().' - '.$category->getName())
              : $category->getName()
            ;
          }

          $gaItems[$order['number']][] = $gaItem;
        } if (isset($productData)) unset($productData);
      }
      // }

      // добавить названия услуг {
      if (isset($order['service']) && is_array($order['service']))
      {
        $services = ServiceTable::getInstance()->getListByCoreIds(array_map(function($i) {
          return $i['service_id'];
        }, $order['service']));

        $serviceById = array();
        foreach ($services as $service)
        {
          /* @var $service Service */
          $serviceById[$service->getId()] = $service;
        }

        foreach ($order['service'] as &$serviceData)
        {
          if (!array_key_exists($serviceData['service_id'], $serviceById)) continue;

          $serviceData['name'] = $serviceById[$serviceData['service_id']]->getName();

          $gaItem = new Order_GaItem();
          $gaItem->orderNumber = $order['number'];
          $gaItem->article = $serviceById[$serviceData['service_id']]->getToken();
          $gaItem->name = $serviceById[$serviceData['service_id']]->getName();
          $gaItem->price = $serviceData['price'];
          $gaItem->quantity = $serviceData['quantity'];
          $gaItem->categoryName = 'Услуга F1';

          $gaItems[$order['number']][] = $gaItem;
        } if (isset($serviceData)) unset($serviceData);
      }
      // }

      // добавить название региона
      $shop = !empty($order['shop_id']) ? ShopTable::getInstance()->getByCoreId($order['shop_id']) : null;
      $order['shop'] = $shop ? array('name' => $shop['name']) : null;

      if (!isset($order['product']))
      {
        $order['product'] = array();
      }
      if (!isset($order['service']))
      {
        $order['service'] = array();
      }

    } if (isset($order)) unset($order);

    //dump($orders, 1);


    $this->paymentForm = false;
    // онлайн оплата?
    if (1 == count($orders) && empty($orderNumber))
    {
      $order = $orders[0];

      //$paymentMethod = !empty($order['payment_id']) ? PaymentMethodTable::getInstance()->getByCoreId($order['payment_id']) : null;
      $paymentMethod = RepositoryManager::getPaymentMethod()->getById($order['payment_id']);

      $isCredit = false;
      if ($paymentMethod->getIsOnline() || in_array($paymentMethod->getId(), array(5, 8))) { //'online', 'invoice'
        $provider = $this->getPaymentProvider();
        $this->paymentForm = $this->paymentProvider->getForm($order);

      } elseif ($paymentMethod->isCredit() ) {
          $isCredit = true;
          //print_r($order);
          $creditBank = RepositoryManager::getCreditBank()->getById($order['credit']['credit_bank_id']);
          $creditProviderId = $creditBank->getProviderId();
          $jsCreditData = array();
          if ($creditProviderId == CreditBankEntity::PROVIDER_KUPIVKREDIT) {
              $kupivkreditData = $this->_getKupivkreditData($order);
              $jsCreditData['widget'] = 'kupivkredit';
              //брокеру отпрвляем стоимость только продуктов!
              $productsSum = 0;
              foreach ($order['product'] as $product) {
                $productsSum += $product['quantity'] * $product['price'];
              }
              $jsCreditData['vars'] = array(
                  'sum' => $productsSum,
                  'order' => $kupivkreditData,
                  'sig' => $this->_signKupivkreditMessage($kupivkreditData)
              );

          } elseif ($creditProviderId == CreditBankEntity::PROVIDER_DIRECT_CREDIT) {

              $jsCreditData['widget'] = 'direct-credit';
              $jsCreditData['vars'] = array(
                  'number' => $order['number'],
                  'items' => array()
              );
              foreach ($order['product'] as $product) {
                  //получаем token БЮ
                  $categoryToken = '';
                  $productOb = $productsById[$product['product_id']];
                  if (!empty($productOb)) {
                      $catList = $productsById[$product['product_id']]->getCategoryList();
                      if (!empty($catList)) {
                          $rootCat = reset($catList);
                          if (!empty($rootCat)) {
                              $categoryToken = $rootCat->getToken();
                          }
                      }
                  }
                  $creditDataType = CreditBankRepository::getCreditTypeByCategoryToken($categoryToken);

                  $jsCreditData['vars']['items'][] = array(
                      'name' => $product['name'],
                      'quantity' => $product['quantity'],
                      'price' => $product['price'],
                      'articul' => $productsById[$product['product_id']]->getArticle(),
                      'type' => $creditDataType,
                  );
              }
          }
          $this->setVar('jsCreditArray', $jsCreditData, true);
          $this->setVar('jsCreditData', json_encode($jsCreditData), true);
      }
    }

    $this->setVar('isCredit', $isCredit, true);
    $this->setVar('orders', $orders, true);
    $this->setVar('gaItems', $gaItems, true);
  }

  public function executePayment(sfWebRequest $request)
  {
    $orderIds = array(316892);

    $result = Core::getInstance()->query('order.get', array(
      'id'     => $orderIds,
      'expand' => array('geo', 'user', 'product', 'service'),
    ));

    $order = array_shift($result);

    $this->paymentProvider = $this->getPaymentProvider();
    $this->paymentForm = $this->paymentProvider->getForm($order);
  }

  private function _getKupivkreditData($order) {

    $data = array();
    $data['items'] = array();
    foreach ($order['product'] as $product) {
      $data['items'][] = array(
        'title' => $product['name'],
        'category' => '',
        'qty' => $product['quantity'],
        'price' => $product['price']
      );
    }
    $data['details'] = array(
      'firstname' => $order['first_name'],
      'lastname' => $order['last_name'],
      'middlename' => $order['middle_name'],
      'email' => '',
      'cellphone' => $order['mobile'],
    );

    $kupivkreditConfig = sfConfig::get('app_credit_provider');
    $kupivkreditConfig = $kupivkreditConfig['kupivkredit'];
    $data['partnerId'] = $kupivkreditConfig['partnerId'];
    $data['partnerName'] = $kupivkreditConfig['partnerName'];
    $data['partnerOrderId'] = $order['number'];
    $data['deliveryType'] = '';

//      print_r($data);
//      die();
    $base64 = base64_encode(json_encode($data));
    return $base64;
  }

  private function _signKupivkreditMessage($message, $iterationCount = 1102) {
    $kupivkreditConfig = sfConfig::get('app_credit_provider');
    $salt = $kupivkreditConfig['kupivkredit']['signature'];
    $message = $message.$salt;
    $result = md5($message).sha1($message);
    for($i = 0; $i < $iterationCount; $i++)
      $result = md5($result);
    return $result;
  }


  /**
   * @param Order|null $order
   * @return OrderDefaultForm
   */
  private function getOrderForm($order = null)
  {
    /* @var $user myUser */
    $user = $this->getUser();

    $productsInCart = array();
    foreach ($user->getCart()->getProducts() as $productId => $product)
    {
      /** @var $product \light\ProductCartData */
      $productsInCart[] = array('id' => $productId, 'quantity' => $product->getQuantity());
    }

    $servicesInCart = array();
    $serviceList = $user->getCart()->getServices();
    foreach($serviceList as $serviceId => $service){
      if (!array_key_exists(0, $service))
      {
        if ($service instanceof ServiceCartData)
        {
          $serviceObj = $service;
        }
        else
        {
          continue;
        }
      }
      else
      {
        $serviceObj = $service[0];
      }
      $servicesInCart[] = array('id' => $serviceId, 'quantity' => $serviceObj->getQuantity());
    }

    $deliveryTypes = array();
    $result = Core::getInstance()->query('order.calc-delivery', array(), array(
      'geo_id'  => $user->getRegion('id'),
      'product' => $productsInCart,
      'service' => $servicesInCart,
    ));
    if (!$result)
    {
      //dump(Core::getInstance()->getError(), 1);
      if ($errors = Core::getInstance()->getError())
      {
        if (is_array($errors) && array_key_exists('product_error_list', $errors))
        {
          $this->errors = $errors['product_error_list'];
        }
      }
      else {
        $this->getLogger()->err('{Order} calculate: empty response from core');
        $this->message = 'Невозможно доставить выбранные товары';
      }

      return false;
    }

    foreach ($result as $k => $item)
    {
      if ('unavailable' == $k)
      {
        continue;
      }

      $deliveryData = DeliveryTypeTable::getInstance()->createQuery()->select('name, description')->where('core_id = ?', $item['mode_id'])->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);
      $item['name'] = $deliveryData['name'];
      $item['desc'] = $deliveryData['description'];
      $item['description'] = $deliveryData['description'];
      $deliveryTypes[] = new DeliveryTypeEntity($item); //RepositoryManager::getDeliveryType()->create($item);
    }
    //myDebug::dump($deliveryTypes, 1);

    // если нет способов доставки
    //myDebug::dump($deliveryTypes, 1);
    if (!count($deliveryTypes))
    {
      $this->getLogger()->err('{Order} calculate: empty delivery\'s types');
      $this->redirect('cart');
    }

    return new OrderDefaultForm($order, array('user' => $this->getUser(), 'deliveryTypes' => $deliveryTypes));
  }

  private function saveOrder(Order $baseOrder, $deliveryMap)
  {
    /* @var myUser */
    $user = $this->getUser();

    $orders = array();
    foreach ($deliveryMap['deliveryTypes'] as $deliveryTypeData)
    {
      if (empty($deliveryTypeData['items'])) continue;

      $deliveryType = !empty($deliveryTypeData['id']) ? DeliveryTypeTable::getInstance()->getByCoreId($deliveryTypeData['id']) : null;
      if (!$deliveryType) continue;

      /* @var $order Order */
      $order = clone $baseOrder;

      $order->mapValue('ProductItem', array());
      $order->mapValue('ServiceItem', array());
      $order->delivery_type_id = null;
      $order->DeliveryType = $deliveryType;
      $order->Status = OrderStatusTable::getInstance()->findOneByToken('created');
      $order->delivered_at = date_format(new DateTime($deliveryTypeData['date']), 'Y-m-d');
      $order->mapValue('delivery_period', !empty($deliveryTypeData['interval']) ? explode(',', $deliveryTypeData['interval']) : null);

      if ('self' == $deliveryType->token)
      {
        $shop = !empty($deliveryTypeData['shop']['id']) ? ShopTable::getInstance()->getByCoreId($deliveryTypeData['shop']['id']) : null;
        if (!$shop) continue;

        $order->address = null;
        $order->shop_id = $shop ? $shop->id : null;
      }
      else {
        $order->shop_id = null;
      }

      $productItems = array();
      $serviceItems = array();
      foreach ($deliveryTypeData['items'] as $itemToken)
      {
        list($itemType, $itemId) = explode('-', $itemToken);
        if ('product' == $itemType)
        {
          /* @var $product Product */
          $cartData = $user->getCart()->getProduct($itemId);
          if(!is_null($cartData)){
            /** @var $cartData light\ProductCartData */
            $productItem = array(
              'id'       => $cartData->getProductId(),
              'quantity' => $cartData->getQuantity() ,
            );

            // дополнительные гарантии для товара
            if ($warrantyData = $user->getCart()->getWarrantyByProduct($cartData->getProductId())) {
              /** @var $warrantyData light\WarrantyCartData */
              $productItem['additional_warranty'] = array(
                array(
                'id'       => $warrantyData->getId(),
                'quantity' => $warrantyData->getQuantity(),
                ),
              );
            }

            $productItems[] = $productItem;
          }
        }
        if ('service' == $itemType)
        {
          /* @var $service Service */
          $cartData = $user->getCart()->getService($itemId);

          foreach ($cartData['products'] as $productId => $service)
          {
            $serviceItems[] = array(
              'id'       => $itemId,
              'quantity' => $service['quantity'],
            );
          }
        }
      }

      // сбор услуг, привязаных к товарам
      $servicesForProduct = array();
      foreach ($user->getCart()->getServices() as $serviceId => $service)
      {
        foreach ($service as $productId => $product)
        {
          /** @var $product \light\ServiceCartData */

          if (0 == $productId) continue;

          if (!array_key_exists($serviceId, $servicesForProduct))
          {
            $servicesForProduct[$serviceId] = array(
              'id'       => $serviceId,
              'quantity' => $product->getQuantity(),
            );
          }
          else {
            $servicesForProduct[$serviceId]['quantity'] += $product->getQuantity();
          }
        }
      }

      $serviceItems = array_merge($serviceItems, array_values($servicesForProduct));
      foreach ($serviceItems as $i => $serviceItem) {
        if (!$serviceItem['quantity']) {
          unset($serviceItems[$i]);
        }
      }

      $order->ProductItem = $productItems;
      $order->ServiceItem = $serviceItems;

      // расчет доставки: рассчитывается в ядре
      $deliveryPrice = 0;
      //$order->sum = $user->getCart()->getTotalForOrder($order) + $deliveryPrice;

      $orders[] = $order;
    }

      $coreData = array_map(function($order) use ($user) {
      /* @var $order Order */
      /* @var $user myUser */
      $return = $order->exportToCore();
      $return['user_id'] = $user->getGuardUser() ? $user->getGuardUser()->getId() : null;
      $return['geo_id'] = $user->getRegion('core_id');
      $return['delivery_period'] = $order->delivery_period;
      $return['payment_id'] =  $order->payment_method_id;
      $return['credit_bank_id'] =  $order->credit_bank_id;
      $return['product'] = $order->ProductItem;
      $return['service'] = $order->ServiceItem;
      $return['address_metro'] = $order->address_metro;
      $return['address_street'] = $order->address_street;
      $return['address_number'] = $order->address_number;
      $return['address_building'] = $order->address_building;
      $return['address_apartment'] = $order->address_apartment;
      $return['address_floor'] = $order->address_floor;

        return $return;
    }, $orders);
    //dump($coreData, 1);
    $response = Core::getInstance()->query('order.create-packet', array(), $coreData, true);
    //dump($response, 1);
    if (is_array($response) && array_key_exists('confirmed', $response) && $response['confirmed'])
    {
      return $response['orders'];
    }
    else {
      throw new Exception('Не удалось принять заказ');
    }
  }

  /**
   * @param DeliveryType $deliveryType
   * @param Shop $shop
   * @return Order_DeliveryMapView
   */
  private function getDeliveryMapView($deliveryType = null, $shop = null)
  {
    $this->getContext()->getConfiguration()->loadHelpers('Date');

    /* @var myUser */
    $user = $this->getUser();

    // товары в корзине для запроса к ядру
    $productsInCart = array();
    foreach ($user->getCart()->getProducts() as $productId => $product)
    {
      /** @var $product light\ProductCartData */
      $productsInCart[] = array('id' => $productId, 'quantity' => $product->getQuantity());
    }

    // услуги в корзине для запроса к ядру
    $servicesInCart = array();
    $serviceList = $user->getCart()->getServices();
    foreach ($serviceList as $serviceId => $service)
    {
      if (!array_key_exists(0, $service)) continue;

      /** @var $tmp \light\ServiceCartData */
      $tmp = $service[0];

      $servicesInCart[] = array('id' => $serviceId, 'quantity' => $tmp->getQuantity());
    }

    // услуги к товарам
    $servicesForProduct = array();
    foreach ($user->getCart()->getServices() as $serviceId => $service)
    {
      foreach ($service as $productId => $productData)
      {
        if (0 == $productId) continue;
        /** @var $productData \light\ServiceCartData */
        if (!array_key_exists($productId, $servicesForProduct))
        {
          $servicesForProduct[$productId] = array();
        }

        //@TODO нужно переделать в следующем хотфиксе
        $coreData = array_shift(Core::getInstance()->query('service.get', array('id' => $serviceId, 'expand' => array())));

        if ($productData->getQuantity()) {
          $servicesForProduct[$productId][] = array(
            'id'       => $serviceId,
            'name'     => $coreData['name'],
            'token'    => $coreData['token'],
            'quantity' => $productData->getQuantity(),
            'price'    => $productData->getPrice(),
          );
        }
      }
    }

    //$result = json_decode(file_get_contents(__DIR__.'/../data/order-calc.json'), true);
    $result = Core::getInstance()->getDeliveryMap(
      $user->getRegion('core_id'),
      $productsInCart,
      $servicesInCart,
      $deliveryType ? $deliveryType->getToken() : null,
      $shop ? $shop->getId() : null
    );
    if (!$result)
    {
      $this->message = 'Товары не могут быть доставлены';

      $this->setTemplate('error');
      return sfView::SUCCESS;
    }

    $deliveryMapView = new Order_DeliveryMapView();

    $deliveryMapView->unavailable = array();
    if (array_key_exists('unavailable', $result)) foreach ($result['unavailable'] as $itemType => $itemIds)
    {
      $deliveryMapView->unavailable = array_merge($deliveryMapView->unavailable, array_map(function($id) use ($itemType) {
        return ('products' == $itemType ? 'product' : 'service').'-'.$id;
      }, $itemIds));
    }

    // сборка магазинов
    foreach ($result['shops'] as $coreData)
    {
      $shopView = new Order_ShopView();
      $shopView->address = $coreData['address'];
      $shopView->id = $coreData['id'];
      $shopView->latitude = $coreData['coord_lat'];
      $shopView->longitude = $coreData['coord_long'];
      $shopView->name = $coreData['name'];
      $shopView->regime = $coreData['working_time'];

      $deliveryMapView->shops[$shopView->id] = $shopView;
    }

    // сборка товаров и услуг
    foreach (array('products', 'services') as $itemType) {
      foreach ($result[$itemType] as $coreData)
      {
        $r = Core::getInstance()->query('products' == $itemType ? 'product.get' : 'service.get', array(
            'id'     => $coreData['id'],
            'expand' => array(),
        ));
        $recordData = array_shift($r);

        if('products' == $itemType){
          $cartElem = $user->getCart()->getProduct($recordData['id']);
          if(!$cartElem){
            continue;
          }
          /** @var $cartElem light\ProductCartData */
          $cartData = array(
            'id' => $cartElem->getProductId(),
            'token' => $recordData['token'],
            'quantity' => $cartElem->getQuantity(),
            'price' => $cartElem->getPrice(),
          );
        }
        else{
          $cartElem = $user->getCart()->getService($recordData['id']);
          if(!$cartElem){
            continue;
          }
          $cartData = array(
            'id' => $cartElem['id'],
            'token' => $recordData['token'],
            'products' => array(),
          );

          foreach($cartElem['products'] as $productId => $service){
            $cartData['products'][$productId] = array('quantity' => $service['quantity'], 'price' => $service['price']);
          }

          if(array_key_exists(0, $cartElem['products'])){
            /** @var $tmp light\ServiceCartData */
            $tmp = $cartElem['products'][0];
            $cartData = array(
              'quantity' => $tmp['quantity'],
              'price' => $tmp['price'],
            );
          }
        }

        $serviceTotal = 0; $serviceName = '';
        if (('products' == $itemType) && array_key_exists($coreData['id'], $servicesForProduct))
        {
          foreach ($servicesForProduct[$coreData['id']] as $service)
          {
            $serviceName .= " + <span class='motton'>{$service['name']} ({$service['quantity']} шт.)</span>";
            $serviceTotal += ($service['price'] * $service['quantity']);
          }
        }

        // дополнительные гарантии для товара
        if ($warrantyData = $user->getCart()->getWarrantyByProduct($coreData['id'])) {
          // нижеследующее нужно для того, чтобы получить название гарантии
          $productV2 = RepositoryManager::getProduct()->getById($coreData['id'], true);
          foreach ($productV2->getWarrantyList() as $w) {
            if ($w->getId() == $warrantyData->getId()) {
              $serviceName .= sprintf(' + <span class="motton">%s (%s шт.)</span>', $w->getName(), $warrantyData->getQuantity());
              $serviceTotal += ($warrantyData->getPrice() * $warrantyData->getQuantity());
            }
          }
        }

        $itemView = new Order_ItemView();
        $itemView->url = $coreData['link'];
        $itemView->deleteUrl =
          'products' == $itemType
          ? $this->generateUrl('cart_delete', array('product' => $recordData['id']), true)
          : $this->generateUrl('cart_service_delete', array('service' => $recordData['id'], 'product' => 0), true)
        ;
        $itemView->addUrl =
          'products' == $itemType
          ? $this->generateUrl('cart_add', array('product' => $recordData['id'], 'quantity' => $coreData['stock']))
          : ''
        ;
        $itemView->id = $coreData['id'];
        $itemView->name = $coreData['name'].$serviceName;
        //$itemView->image = ProductTable::getInstance()->getMainPhotoUrl($recordData, 0);
        $itemView->image = 'product' == $itemType ? $coreData['media_image'] : ($coreData['media_image'] ? $coreData['media_image'] : '/images/f1info.png');
        $itemView->price = $coreData['price'];
        $itemView->quantity = $cartData['quantity'];
        $itemView->total = ($cartData['price'] * $cartData['quantity']) + $serviceTotal;
        $itemView->type = 'products' == $itemType ? Order_ItemView::TYPE_PRODUCT : Order_ItemView::TYPE_SERVICE;
        $itemView->token = $itemView->type.'-'.$itemView->id;
        $itemView->stock = isset($coreData['stock']) ? $coreData['stock'] : 0;

        foreach ($coreData['deliveries'] as $deliveryToken => $deliveryData)
        {
          $deliveryView = new Order_DeliveryView();
          $deliveryView->price = $deliveryData['price'];
          $deliveryView->token = $deliveryToken;
          $deliveryView->name = 0 === strpos($deliveryToken, 'self') ? 'В самовывоз' : 'В доставку';

          foreach ($deliveryData['dates'] as $dateData)
          {
            $dateView = new Order_DateView();
            $dateView->day = date('j', strtotime($dateData['date']));
            $dateView->dayOfWeek = format_date($dateData['date'], 'EEE', 'ru');
            $dateView->value = date('Y-m-d', strtotime($dateData['date']));
            $dateView->timestamp = strtotime($dateData['date'], 0) * 1000;

            foreach ($dateData['interval'] as $intervalData)
            {
              $intervalView = new Order_IntervalView();
              $intervalView->start_at = $intervalData['time_begin'];
              $intervalView->end_at = $intervalData['time_end'];

              $dateView->intervals[] = $intervalView;
            }

            $deliveryView->dates[] = $dateView;
          }

          $itemView->deliveries[$deliveryView->token] = $deliveryView;
        }

        $deliveryMapView->items[$itemView->token] = $itemView;
      }
    }

    // сборка типов доставки
    foreach ($result['deliveries'] as $deliveryTypeToken => $coreData)
    {
      $recordData = DeliveryTypeTable::getInstance()->createQuery()->where('token = ?', $coreData['token'])->fetchOne(array(), Doctrine::HYDRATE_ARRAY);

      $deliveryTypeView = new Order_DeliveryTypeView();
      $deliveryTypeView->description = $recordData['description'];
      $deliveryTypeView->id = $coreData['mode_id'];
      $deliveryTypeView->name = $recordData['name'];
      $deliveryTypeView->type = $recordData['token'];
      $deliveryTypeView->token = $deliveryTypeToken;
      $deliveryTypeView->shortName = 0 === strpos($deliveryTypeView->type, 'self') ? 'Самовывоз' : 'Доставим';

      $deliveryTypeView->shop =
        array_key_exists($coreData['shop_id'], $deliveryMapView->shops)
          ? $deliveryMapView->shops[$coreData['shop_id']]
          : null
      ;

      foreach ($deliveryMapView->items as $itemView)
      {
        if (($itemView->type == Order_ItemView::TYPE_PRODUCT) && !in_array($itemView->id, $coreData['products'])) continue;
        if (($itemView->type == Order_ItemView::TYPE_SERVICE) && !in_array($itemView->id, $coreData['services'])) continue;

        $deliveryTypeView->items[] = $itemView->token;
      }

      $tmpDates = null;
      $dates = array();
      foreach ($deliveryTypeView->items as $itemToken)
      {
        $dates = array_map(function($i) { return $i->value; }, $deliveryMapView->items[$itemToken]->deliveries[$deliveryTypeView->token]->dates);
        $dates = is_array($tmpDates) ? array_intersect($dates, $tmpDates) : $dates;
        $tmpDates = $dates;
      }
      $deliveryTypeView->date = array_shift($dates);
      $deliveryTypeView->displayDate = format_date($deliveryTypeView->date, 'd MMMM', 'ru');

      $interval =
        (isset($deliveryTypeView->items[0]) && isset($deliveryMapView->items[$deliveryTypeView->items[0]]->deliveries[$deliveryTypeView->token]->dates[0]->intervals[0]))
        ? $deliveryMapView->items[$deliveryTypeView->items[0]]->deliveries[$deliveryTypeView->token]->dates[0]->intervals[0]
        : null
      ;
      $deliveryTypeView->interval = ($interval) ? ($interval->start_at.','.$interval->end_at) : null;
      $deliveryTypeView->displayInterval = ($interval) ? ('с '.$interval->start_at.' по '.$interval->end_at) : null;

      $deliveryMapView->deliveryTypes[$deliveryTypeView->token] = $deliveryTypeView;
    }

    foreach ($deliveryMapView->items as $itemView)
    {
      foreach ($itemView->deliveries as $deliveryView)
      {
        $deliveryView->name .= ''
          .(
            $deliveryMapView->deliveryTypes[$deliveryView->token]->shop
            ? (' '.str_replace('г. Москва,', '', $deliveryMapView->deliveryTypes[$deliveryView->token]->shop->address))
            : ''
          )
        ;
      }
    }

    //var_dump($deliveryMapView); die();

    return $deliveryMapView;
  }

  /**
   *
   * @param type $name
   * @return PsbankPaymentProvider
   */
  private function getPaymentProvider($name = null)
  {
    if (null == $name) {
      $name = sfConfig::get('app_payment_default_provider');
    }

    $providers = sfConfig::get('app_payment_provider');
    $class = sfInflector::camelize($name.'payment_provider');

    return new $class($providers[$name]);
  }

  private function getCookieKeys()
  {
    return array(
      'recipient_first_name',
      'recipient_last_name',
      'recipient_phonenumbers',
      //'address_metro',
      'address_street',
      'address_number',
      'address_building',
      'address_apartment',
      'address_floor',
    );
  }
}
