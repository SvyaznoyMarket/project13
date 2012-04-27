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

    if (true
      && (0 == count($user->getCart()->getProducts()))
      && (0 == count($user->getCart()->getServices()))
    ) {
      $this->redirect('cart');
    }

    $this->order = new Order();
    $this->order->region_id = $this->getUser()->getRegion('id');
    $this->form = $this->getOrderForm($this->order);
    if (!$this->form)
    {
      $this->setTemplate('error');

      return sfView::SUCCESS;
    }

    $deliveryTypes = $this->form->getOption('deliveryTypes');
    $defaultDeliveryType = (1 == count($deliveryTypes)) ? $deliveryTypes[0] : null;
    $deliveryMap = $this->getDeliveryMapView($defaultDeliveryType);

    $this->setVar('deliveryMap', $deliveryMap, true);
    $this->setVar('mapCenter', json_encode(array('latitude' => $user->getRegion('latitude'), 'longitude' => $user->getRegion('longitude'))));

    // получение ссылки "Вернуться к покупкам"
    $this->backLink = $this->generateUrl('cart');
    $product = array_pop($user->getCart()->getProducts());
    if (!empty($product['id']))
    {
      $products = Core::getInstance()->query('product.get', array('id' => $product['id'], 'expand' => array('category')));
      if (!empty($products[0]['category'][0]['link']))
      {
        $this->backLink = $products[0]['category'][0]['link'];
      }
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

    $form = $this->getOrderForm($this->order);
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      $baseOrder = $form->updateObject();

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
   * Executes complete action
   *
   * @param sfRequest $request A request object
   */
  public function executeComplete(sfWebRequest $request)
  {
    $request->setParameter('_template', 'order_complete');

    /* @var myUser */
    $user = $this->getUser();

    $orderIds = $user->getFlash('complete_orders');
    if (empty($orderIds))
    {
      $this->redirect('cart');
    }

    $result = Core::getInstance()->query('order.get', array(
      'id'     => $orderIds,
      'expand' => array('geo', 'user', 'product', 'service'),
    ));
    //myDebug::dump($result);
    if (!$result)
    {
      $this->getLogger()->err('{Order} get list: empty response from core');
    }
    $orders = $result;

    $this->paymentForm = false;
    // онлайн оплата?
    if (1 == count($orders))
    {
      $order = $orders[0];

      $paymentMethod = !empty($order['payment_id']) ? PaymentMethodTable::getInstance()->getByCoreId($order['payment_id']) : null;
      if ('online' == $paymentMethod->token)
      {
        // добавить названия товаров {
        if (isset($order['product']) && is_array($order['product']))
        {
          $products = RepositoryManager::getProduct()->getListById(array_map(function($i) {
            return $i['product_id'];
          }, $order['product']));

          $productsById = array();
          foreach ($products as $product)
          {
            /* @var $product ProductEntity */
            $productsById[$product->getId()] = $product;
          }

          foreach ($order['product'] as &$productData)
          {
            $productData['name'] = array_key_exists($productData['product_id'], $productsById) ? $productsById[$productData['product_id']]->getName() : '';
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
            $serviceData['name'] = array_key_exists($serviceData['service_id'], $serviceById) ? $serviceById[$serviceData['service_id']]->getName() : '';
          } if (isset($serviceData)) unset($serviceData);
        }
        // }

        $provider = $this->getPaymentProvider();
        $this->paymentForm = $provider->getForm($order);
      }
    }

    $user->setFlash('complete_orders', $orderIds);

    $this->setVar('orders', $orders, true);
  }


  /**
   * @param Order|null $order
   * @return OrderDefaultForm
   */
  private function getOrderForm($order = null)
  {
    /* @var $user myUser */
    $user = $this->getUser();

    $regions = RegionTable::getInstance()->getListForOrder(array_map(function($i) { return $i['id']; }, $this->getUser()->getCart()->getProducts()));

    $productsInCart = array();
    foreach ($user->getCart()->getProducts() as $product)
    {
      $productsInCart[] = array('id' => $product['id'], 'quantity' => $product['quantity']);
    }

    $servicesInCart = array();
    foreach ($user->getCart()->getServices() as $service)
    {
      // если услуга не принадлежит товару 0, то пропустить
      if (!array_key_exists(0, $service['products'])) continue;

      $servicesInCart[] = array('id' => $service['id'], 'quantity' => $service['products'][0]['quantity']);
    }

    $deliveryTypes = array();
    $result = Core::getInstance()->query('order.calc-delivery', array(), array(
      'geo_id'  => $user->getRegion('core_id'),
      'product' => $productsInCart,
      'service' => $servicesInCart,
    ));
    //myDebug::dump($result, 1);
    if (!$result)
    {
      $this->getLogger()->err('{Order} calculate: empty response from core');
      $this->message = 'Невозможно доставить выбранные товары';

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
      $deliveryTypes[] = RepositoryManager::getDeliveryType()->create($item);
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
      $order->User = $user->getGuardUser();
      $order->Status = OrderStatusTable::getInstance()->findOneByToken('created');
      $order->delivered_at = date_format(new DateTime($deliveryTypeData['date']), 'Y-m-d 00:00:00');
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

      $productItems = array(); $serviceItems = array();
      foreach ($deliveryTypeData['items'] as $itemToken)
      {
        list($itemType, $itemId) = explode('-', $itemToken);
        if ('product' == $itemType)
        {
          /* @var $product Product */
          $cartData = $user->getCart()->getProduct($itemId);
          $productItems[] = array(
            'id'       => $itemId,
            'quantity' => $cartData['quantity'],
          );
        }
        if ('service' == $itemType)
        {
          /* @var $service Service */
          $cartData = $user->getCart()->getService($itemId);

          foreach ($cartData['products'] as $productId => $productData)
          {
            if (empty($productData['quantity'])) continue;
            $serviceItems[] = array(
              'id'       => $itemId,
              'quantity' => $productData['quantity'],
            );
          }
        }
      }

      // сбор услуг, привязаных к товарам
      $servicesForProduct = array();
      foreach ($user->getCart()->getServices() as $serviceId => $service)
      {
        foreach ($service['products'] as $productId => $product)
        {
          if (empty($productId)) continue;

          if (!array_key_exists($service['id'], $servicesForProduct))
          {
            $servicesForProduct[$service['id']] = array(
              'id'       => $service['id'],
              'quantity' => $service['products'][$productId]['quantity'],
            );
          }
          else {
            $servicesForProduct[$service['id']]['quantity'] += $service['products'][$productId]['quantity'];
          }
        }
      }

      $serviceItems = array_merge($serviceItems, array_values($servicesForProduct));

      $order->ProductItem = $productItems;
      $order->ServiceItem = $serviceItems;

      // расчет доставки: рассчитывается в ядре
      $deliveryPrice = 0;
      //$order->sum = $user->getCart()->getTotalForOrder($order) + $deliveryPrice;

      $orders[] = $order;
    }

    $coreData = array_map(function($order) {
      /* @var $order Order */
      $return = $order->exportToCore();
      $return['delivery_period'] = $order->delivery_period;
      $return['product'] = $order->ProductItem;
      $return['service'] = $order->ServiceItem;

      return $return;
    }, $orders);
    //dump($coreData, 1);
    $response = Core::getInstance()->query('order.create-packet', array(), $coreData, true);
    //myDebug::dump($response, 1);
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
    foreach ($user->getCart()->getProducts() as $product)
    {
      $productsInCart[] = array('id' => $product['id'], 'quantity' => $product['quantity']);
    }

    // услуги в корзине для запроса к ядру
    $servicesInCart = array();
    foreach ($user->getCart()->getServices() as $service)
    {
      if (!isset($service['products'][0])) continue;

      $servicesInCart[] = array('id' => $service['id'], 'quantity' => $service['products'][0]['quantity']);
    }

    // услуги к товарам
    $servicesForProduct = array();
    foreach ($user->getCart()->getServices() as $service)
    {
      foreach ($service['products'] as $productId => $productData)
      {
        if (empty($productId)) continue;

        if (!array_key_exists($productId, $servicesForProduct))
        {
          $servicesForProduct[$productId] = array();
        }

        $coreData = array_shift(Core::getInstance()->query('service.get', array('id' => $service['id'], 'expand' => array())));

        $servicesForProduct[$productId][] = array(
          'id'       => $service['id'],
          'name'     => $coreData['name'],
          'token'    => $service['token'],
          'quantity' => $productData['quantity'],
          'price'    => $productData['price'],
        );
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
    //$deliveryMapView->unavailable = array('product-23595');
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
        $recordData = array_shift(Core::getInstance()->query('products' == $itemType ? 'product.get' : 'service.get', array(
          'id'     => $coreData['id'],
          'expand' => array(),
        )));
        $cartData =
          'products' == $itemType
          ? $user->getCart()->getProduct($recordData['id'])
          : $user->getCart()->getService($recordData['id'])
        ;

        if (('services' == $itemType) && isset($cartData['products'][0]))
        {
          $cartData['quantity'] = $cartData['products'][0]['quantity'];
          $cartData['price'] = $cartData['products'][0]['price'];
        }

        $serviceTotal = 0; $serviceName = '';
        if (('products' == $itemType) && array_key_exists($coreData['id'], $servicesForProduct))
        {
          foreach ($servicesForProduct[$coreData['id']] as $service)
          {
            $serviceName .= " + {$service['name']} ({$service['quantity']} шт.)";
            $serviceTotal += ($service['price'] * $service['quantity']);
          }
        }

        $itemView = new Order_ItemView();
        $itemView->deleteUrl =
          'products' == $itemType
          ? $this->generateUrl('cart_delete', array('product' => $recordData['id']))
          : $this->generateUrl('cart_service_delete', array('service' => $recordData['id'], 'product' => 0))
        ;
        $itemView->id = $coreData['id'];
        $itemView->name = $coreData['name'].$serviceName;
        //$itemView->image = ProductTable::getInstance()->getMainPhotoUrl($recordData, 0);
        $itemView->image = $coreData['media_image'];
        $itemView->price = $coreData['price'];
        $itemView->quantity = $cartData['quantity'];
        $itemView->total = ($cartData['price'] * $cartData['quantity']) + $serviceTotal;
        $itemView->type = 'products' == $itemType ? Order_ItemView::TYPE_PRODUCT : Order_ItemView::TYPE_SERVICE;
        $itemView->url = '';
          //'products' == $itemType
          //  ? $coreData['link']
          //  : $this->generateUrl('service_show', array('service' => ServiceTable::getInstance()->createQuery()->select('token')->where('core_id = ?', $coreData['id'])->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR)))
        //;
        $itemView->token = $itemView->type.'-'.$itemView->id;
        $itemView->stock = isset($coreData['stock']) ? $coreData['stock'] : 0;
        $itemView->addUrl =
          'products' == $itemType
            ? $this->generateUrl('cart_add', array('product' => $recordData['id'], 'quantity' => $coreData['stock']))
            : ''
        ;

        foreach ($coreData['deliveries'] as $deliveryToken => $deliveryData)
        {
          $deliveryView = new Order_DeliveryView();
          $deliveryView->price = $deliveryData['price'];
          $deliveryView->token = $deliveryToken;
          $deliveryView->name = 0 === strpos($deliveryToken, 'self') ? 'В самовывоз' : 'В доставку';

          // если нет дат для услуг
          if ($itemView->type == Order_ItemView::TYPE_SERVICE)
          {
            $deliveryData['dates'] = array();
            $now = time();
            $time = $time = strtotime("+1 day", $now);
            foreach (range(1, 7 * 4) as $i)
            {
              $deliveryData['dates'][] = array('date' => date('Y-m-d', $time), 'interval' => array());
              $time = strtotime("+1 day", $time);
            }
          }

          foreach ($deliveryData['dates'] as $dateData)
          {
            $dateView = new Order_DateView();
            $dateView->day = date('j', strtotime($dateData['date']));
            $dateView->dayOfWeek = format_date($dateData['date'], 'EEE', 'ru');
            $dateView->value = date('Y-m-d', strtotime($dateData['date']));
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
   * @return UnitellerPaymentProvider
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
}
