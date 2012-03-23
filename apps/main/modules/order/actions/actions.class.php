<?php

/**
 * order actions.
 *
 * @package    enter
 * @subpackage order
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class orderActions extends myActions
{
  const LAST_STEP = 1;

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
  }

  /**
   * Executes show action
   *
   * @param sfRequest $request A request object
   */
  public function executeShow(sfWebRequest $request)
  {
    $this->order = $this->getRoute()->getObject();
  }

  /**
   * Executes 1click action
   *
   * @param sfRequest $request A request object
   */
  public function execute1click(sfWebRequest $request)
  {
    if (!$request->isXmlHttpRequest())
    {
      $this->redirect($request->getReferer() . '#order1click-link');
    }

    $return = array('success' => false,);

    $this->product = ProductTable::getInstance()->getByBarcode($request->getParameter('product'), array('with_model' => true));

    $this->shop = $request['shop'] ? ShopTable::getInstance()->getByToken($request['shop']) : null;
    $shopData = $this->shop ? array('name' => $this->shop->name, 'region' => $this->shop->Region->name, 'regime' => $this->shop->regime, 'address' => $this->shop->address) : false;

    $quantity = (int)$request->getParameter('quantity');
    if ($quantity <= 0)
    {
      $quantity = 1;
    }
    $this->product->mapValue('cart', array('quantity' => $quantity));

    $this->order = new Order();
    $this->order->User = $this->getUser()->getGuardUser();
    $this->order->Status = OrderStatusTable::getInstance()->findOneByToken('created');
    $this->order->PaymentMethod = PaymentMethodTable::getInstance()->findOneByToken('nalichnie');
    $this->order->shop_id = $this->shop ? $this->shop->id : null;
    $this->order->delivery_type_id = 1;
    $this->order->sum = ProductTable::getInstance()->getRealPrice($this->product) * $quantity; //нужна для правильного отбражения формы заказа
    $this->order->type_id = Order::TYPE_1CLICK;

    if (empty($this->order->region_id))
    {
      $this->order->region_id = $this->getUser()->getRegion('id');
    }

    $this->form = new OrderOneClickForm($this->order, array('user' => $this->getUser()->getGuardUser(), 'quantity' => $quantity,));
    //$this->form['product_quantity']->setDefault(5);
    //$this->form->setValue('product_quantity', 5);
    //$this->form->getValue('product_quantity');

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      // если в запросе нет shop добываем его из параметров формы
      if (!$this->shop)
      {
        $taintedValues = $this->form->getTaintedValues();
        $this->shop = !empty($taintedValues['shop_id']) ? ShopTable::getInstance()->getById($taintedValues['shop_id']) : null;
      }
      // Осторожно: нарушен прынцып DRY!
      $shopData = $this->shop ? array('name' => $this->shop->name, 'region' => $this->shop->Region->name, 'regime' => $this->shop->regime, 'address' => $this->shop->address) : false;

      if ($this->form->isValid())
      {
        $order = $this->form->updateObject();

        if ($this->product->isKit())
        {
          foreach ($this->product->PartRelation as $partRelation)
          {
            $part = ProductTable::getInstance()->getById($partRelation->part_id, array('with_model' => true));

            $part_quantity = 1;
            foreach ($part['KitRelation'] as $KitRelation)
            {
              if ($KitRelation['kit_id'] == $partRelation->kit_id)
              {
                $part_quantity = $KitRelation['quantity'];
                break;
              }
            }

            $relation = new OrderProductRelation();
            $relation->fromArray(array('product_id' => $part['id'], 'price' => ProductTable::getInstance()->getRealPrice($part), 'quantity' => $this->form->getValue('product_quantity') * $part_quantity,));
            $order->ProductRelation[] = $relation;
          }
        } else
        {
          $relation = new OrderProductRelation();
          $relation->fromArray(array('product_id' => $this->product->id, 'price' => ProductTable::getInstance()->getRealPrice($this->product), 'quantity' => $this->form->getValue('product_quantity'),));
          $order->ProductRelation[] = $relation;
        }

        $sum = 0;
        foreach ($order->ProductRelation as $product)
        {
          $sum += $product['price'] * $product['quantity'];
        }
        $this->order->sum = $sum;

        try
        {
          $order->delivery_type_id = !empty($order->shop_id) // если указан магазин, то тип получения заказа - самовывоз
            ? DeliveryTypeTable::getInstance()->getByToken('self')->id : DeliveryTypeTable::getInstance()->getByToken('standart')->id;

          $order->payment_details = 'Это быстрый заказ за 1 клик. Уточните параметры заказа у клиента.';
          $order->save();

          $form = new UserFormSilentRegister();
          $form->bind(array('username' => $order->recipient_phonenumbers, 'first_name' => trim($order->recipient_first_name . ' ' . $order->recipient_last_name),));

          $return['success'] = true;
          $return['message'] = 'Заказ успешно создан';
          $return['data'] = array('title' => 'Ваш заказ принят, спасибо!', 'content' => $this->getPartial($this->getModuleName() . '/complete', array('order' => $order, 'form' => $form, 'shop' => $this->shop)), 'shop' => $shopData,);
        } catch (Exception $e)
        {
          $return['success'] = false;
          $return['message'] = 'Не удалось создать заказ' . (sfConfig::get('sf_debug') ? (' Ошибка: ' . $e->getMessage()) : '');
        }
      } else
      {
        $return = array('success' => false, 'data' => array('form' => $this->getPartial($this->getModuleName() . '/form_oneClick'), 'shop' => $shopData,),);
      }

      return $this->renderJson($return);
    }

    return $this->renderJson(array('success' => true, 'data' => array('form' => $this->getPartial($this->getModuleName() . '/form_oneClick'), 'shop' => $shopData,),));
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

    $this->getResponse()->setTitle('Способ доставки и оплаты  – Enter.ru');

    $this->step = $request->getParameter('step', 1);
    $this->order = $this->getUser()->getOrder()->get();

    $this->order->region_id = $this->getUser()->getRegion('id');
    $this->getUser()->getOrder()->set($this->order);

    $this->form = $this->getOrderForm($this->order);
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $baseOrder = $this->form->updateObject();

        $productData = json_decode($request['products_hash'], true);
        try {
          $result = $this->saveOrder($baseOrder, $productData);
        }
        catch(Exception $e) {
          $result = array('success' => false);
        }

        return $this->renderJson($result);
      }

      return $this->renderJson(array(
        'success' => false,
        'error'   => array('message' => 'Форма содержит ошибки'),
      ));
    }

    $productsInCart = array();
    foreach ($user->getCart()->getProducts() as $product)
    {
      $productsInCart[] = array('id' => $product['core_id'], 'quantity' => $product['cart']['quantity']);
    }

    $servicesInCart = array();
    foreach ($user->getCart()->getServices() as $service)
    {
      // если услуга принадлежит товару, то пропустить
      if (count($service['cart']['product'])) continue;

      $servicesInCart[] = array('id' => $service['core_id'], 'quantity' => $service['cart']['quantity']);
    }

    $deliveryMap = $this->getCore()->getDeliveryMap($user->getRegion('core_id'), $productsInCart, $servicesInCart);

    // группировка услуг по товарам
    $servicesByProduct = array();
    foreach ($user->getCart()->getServices() as $service)
    {
      foreach ($service['cart']['product'] as $product_id => $v)
      {
        if (!array_key_exists($product_id, $servicesByProduct))
        {
          $servicesByProduct[$product_id] = array();
        }
        $servicesByProduct[$product_id][] = $service;
      }
    }

    // url удаления для услуг
    $serviceDeleteUrls = array();
    foreach ($user->getCart()->getServices() as $service)
    {
      if (count($service['cart']['product'])) continue;

      $serviceDeleteUrls[$service->core_id] = $this->generateUrl('cart_service_delete', array(
        'product' => '-/-',
        'service' => $service->token,
      ));
    }

    // url удаления для товаров
    $productDeleteUrls = array();
    foreach ($user->getCart()->getProducts() as $product)
    {
      /* @var $product Product */
      $productDeleteUrls[$product->core_id] = $this->generateUrl('cart_delete', array(
        'product' => $product->token_prefix.'/'.$product->token,
      ));
    }

    if (!is_array($deliveryMap))
    {
      return sfView::ERROR;
    }

    foreach ($deliveryMap as &$item)
    {
      if (array_key_exists('products', $item))
      {
        foreach ($item['products'] as &$productData)
        {
          $product_id = ProductTable::getInstance()->getIdByCoreId($productData['id']);

          if (!array_key_exists($product_id, $servicesByProduct)) continue;

          foreach ($servicesByProduct[$product_id] as $service)
          {
            $productData['name'] .= ' + '.$service->name;
            $productData['price'] += $service->price;
          }
        } if (isset($productData)) unset($productData);
      }
      else if (array_key_exists('shops', $item))
      {
        foreach ($item['shops'] as &$shopData)
        {
          foreach ($shopData['products'] as &$productData)
          {
            $product_id = ProductTable::getInstance()->getIdByCoreId($productData['id']);

            if (!array_key_exists($product_id, $servicesByProduct)) continue;

            foreach ($servicesByProduct[$product_id] as $service)
            {
              $productData['name'] .= ' + '.$service->name;
              $productData['price'] += $service->price;
            }
          } if (isset($productData)) unset($productData);
        } if (isset($shopData)) unset($shopData);
      }
    } if (isset($item)) unset($item);

    // недоступные товары
    if (isset($deliveryMap['unavailable']))
    {
      $productIds = array_keys($deliveryMap['unavailable']);
      $categoryUrl = false;
      if (count($productIds))
      {
        /* @var $product Product */
        $product = ProductTable::getInstance()->getByCoreId($productIds[0]);
        if ($product)
        {
          $categoryUrl = $this->generateUrl('productCatalog_category', $product->getMainCategory());
        }
      }

      $products = array();
      foreach ($productIds as $productId)
      {
        $productId = ProductTable::getInstance()->getIdByCoreId($productId);
        $product = $user->getCart()->getProduct($productId);
        $products[] = array(
          'id'       => $product->core_id,
          'name'     => $product->name,
          'price'    => (int)$product->getRealPrice(),
          'quantity' => $product->cart['quantity'],
        );
      }

      $deliveryMap['unavailable'] = array(
        'products'     => $products,
        'category_url' => $categoryUrl,
      );
    }

    $this->setVar('deliveryMap', json_encode($deliveryMap), true);
    $this->setVar('mapCenter', json_encode(array('latitude' => $user->getRegion('latitude'), 'longitude' => $user->getRegion('longitude'))));

    $this->setVar('productDeleteUrls', $productDeleteUrls, true);
    $this->setVar('serviceDeleteUrls', $serviceDeleteUrls, true);
  }

  /**
   * Executes updateField action
   *
   * @param sfRequest $request A request object
   */
  public function executeUpdateField(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $renderers = array('delivery_period_id' => function($form)
    {
      return myToolkit::arrayDeepMerge(array('' => ''), $form['delivery_period_id']->getWidget()->getChoices());
    }, 'delivered_at' => function($form)
    {
      return myToolkit::arrayDeepMerge(array('' => ''), $form['delivered_at']->getWidget()->getChoices());
    },);

    $field = $request['field'];
    $this->step = $request->getParameter('step', 1);

    $form = new OrderDefaultForm($this->getUser()->getOrder()->get());
    if (isset($form[$field]))
    {
      //$form->useFields(array($field) + array_keys($request->getParameter($form->getName())));
      $form->bind($request->getParameter($form->getName()));

      $order = $form->updateObject();
      $this->getUser()->getOrder()->set($order);

      $result = array('success' => true, 'data' => array('content' => isset($renderers[$field]) ? $renderers[$field]($form) : $this->getPartial($this->getModuleName() . '/field_' . $field, array('form' => $form)),),);
    } else
    {
      $result = array('success' => false);
    }

    return $this->renderJson($result);
  }

  /**
   * Executes edit action
   *
   * @param sfRequest $request A request object
   */
  public function executeEdit(sfWebRequest $request)
  {
  }

  /**
   *
   * @param sfWebRequest $request
   */
  public function executeCancel(sfWebRequest $request)
  {
    $token = $request['token'];
    if (!$token) $this->redirect($this->getRequest()->getReferer());

    $orderL = OrderTable::getInstance()->findBy('token', $token);
    foreach ($orderL as $order) $coreId = $order->core_id;
    //print_r($order->getData());
    $res = Core::getInstance()->query('order.cancel', array('id' => $coreId));
    //если отменилось на ядре, отменим здесь тоже
    if ($res)
    {
      //$order->setData( array('status_id'=>Order::STATUS_CANCELLED));
      //->set('status_id', Order::STATUS_CANCELLED);
      $order->setCorePush(false);
      $order->setArray(array('status_id' => Order::STATUS_CANCELLED));
      $a = $order->save();
    }
    $this->redirect($this->getRequest()->getReferer());
  }


  /**
   * Executes confirm action
   *
   * @param sfRequest $request A request object
   */
  public function executeConfirm(sfWebRequest $request)
  {
    $this->getResponse()->setTitle('Подтверждение заказа – Enter.ru');

    if ($request->isMethod('post'))
    {
      if ('yes' == $request['agree'])
      {
        $this->forward($this->getModuleName(), 'create');
      }
    }

    $order = $this->getUser()->getOrder()->get();
    $this->forwardUnless($order->step, $this->getModuleName(), 'new');
    if ($order->isOnlinePayment())
    {
      $provider = $this->getPaymentProvider();
      $this->paymentForm = $provider->getForm($order);
    }

    $this->setVar('order', $order);
  }

  /**
   * Executes complete action
   *
   * @param sfRequest $request A request object
   */
  public function executeComplete(sfWebRequest $request)
  {
    $provider = $this->getPaymentProvider();
    if (!($this->order = $provider->getOrder($request)))
    {
      $this->order = $this->getUser()->getOrder()->get();
    } else
    {
      $this->result = $provider->getPaymentResult($this->order);
    }

    //$this->redirectUnless($this->order->exists(), 'order_new');

    $this->form = new UserFormSilentRegister();
    $this->form->bind(array('username' => $this->order->recipient_phonenumbers, 'first_name' => trim($this->order->recipient_first_name . ' ' . $this->order->recipient_last_name),));

    if (!$this->form->isValid())
    {
      $this->form = new UserFormBasicRegister(null, array('validate_username' => false));
      $this->form->bind(array('first_name' => trim($this->order->recipient_first_name . ' ' . $this->order->recipient_last_name),));
    }

    $this->getUser()->setCacheCookie();
    $this->getUser()->getCart()->clear();
    $this->getUser()->getOrder()->clear();

    //$this->setVar('order', $this->order, true);
  }

  /**
   * Executes getUser action
   *
   * @param sfRequest $request A request object
   */
  public function executeGetUser(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $user = $this->getUser()->getGuardUser();

    $form = new OrderDefaultForm();

    return $this->renderJson(array('success' => $this->getUser()->isAuthenticated(), 'data' => array('content' => $this->getPartial($this->getModuleName() . '/user'), 'fields' => $user ? array($form['recipient_first_name']->renderName() => $user->first_name, $form['recipient_last_name']->renderName() => $user->last_name, $form['recipient_phonenumbers']->renderName() => $user->phonenumber,) : false,),));
  }

  /**
   * Executes error action
   *
   * @param sfRequest $request A request object
   */
  public function executeError(sfWebRequest $request)
  {
    $this->getUser()->getOrder()->clear();
  }

  /**
   * Executes create action
   *
   * @param sfRequest $request A request object
   */
  public function executeCreate(sfWebRequest $request)
  {
    $this->order = $this->getUser()->getOrder()->get();

    if ($this->saveOrder($this->order))
    {
      $this->redirect($this->order->isOnlinePayment() ? 'order_payment' : 'order_complete');
    } else
    {
      $this->redirect('order_error');
    }
  }

  /**
   * Executes payment action
   *
   * @param sfRequest $request A request object
   */
  public function executePayment(sfWebRequest $request)
  {
    $user = $this->getUser();

    $this->order = $user->hasFlash('order_id') ? OrderTable::getInstance()->getById($user->getFlash('order_id')) : $user->getOrder()->get();

    $this->redirectUnless($this->order->isOnlinePayment(), 'order_new');

    $provider = $this->getPaymentProvider();
    $this->paymentForm = $provider->getForm($this->order);

    $user->setCacheCookie();
    $user->getCart()->clear();
    $user->getOrder()->clear();

    $user->setFlash('order_id', $this->order->id);
  }

  public function executeCallback(sfWebRequest $request)
  {
    $provider = $this->getPaymentProvider();

    $order = $provider->getOrder($request);
    $this->forward404Unless($order);

    $this->result = $provider->getPaymentResult($order);
  }

  /**
   *
   * @param Order $order
   *
   * @return bool
   */
  protected function saveOrder(Order $baseOrder, array $data)
  {
    $coreClient = CoreClient::getInstance();
    $user = $this->getUser();


    $fillOrderProducts = function(Order $order, $data) use ($user)
    {
      /* @var $user myUser */

      foreach ($data as $productData)
      {
        if (empty($productData['is_service']))
        {
          $product_id = ProductTable::getInstance()->getIdByCoreId($productData['id']);

          /* @var $product Product */
          $product = $user->getCart()->getProduct($product_id);
          if (!$product) continue;

          $relation = new OrderProductRelation();
          $relation->setProduct($product);
          $relation->setPrice(ProductTable::getInstance()->getRealPrice($product));
          $relation->setQuantity(1000 + $product->cart['quantity']);

          $order->ProductRelation[] = $relation;
        }
      }
    };

    $fillOrderServices = function(Order $order, $data) use ($user)
    {
      /* @var $user myUser */

      foreach ($data as $serviceData)
      {
        if (!empty($serviceData['is_service']))
        {
          $service_id = ServiceTable::getInstance()->getIdByCoreId($serviceData['id']);

          /* @var $service Service */
          $service = $user->getCart()->getService($service_id);
          if (!$service) continue;

          if ($service->cart['quantity'] > 0)
          {
            $relation = new OrderServiceRelation();
            $relation->setService($service);
            $relation->setPrice($service->price);
            $relation->setQuantity($service->cart['quantity']);

            $order->ServiceRelation[] = $relation;
          }
          if (count($service->cart['product']) > 0)
          {
            foreach ($service->cart['product'] as $product_id => $quantity)
            {
              if (!$product_id || !$quantity) continue;

              $relation = new OrderServiceRelation();
              $relation->setService($service);
              $relation->setProductId($product_id);
              $relation->setPrice($service->price);
              $relation->setQuantity($quantity);

              $order->ServiceRelation[] = $relation;
            }
          }
        }
      }
    };

    $fillOrder = function(Order $order, array $data) use ($user, $coreClient) {
      /* @var $user myUser */
      /* @var $coreClient CoreClient */

      try {
        $r = $coreClient->query('product.get-delivery-price', array(
          'geo_id'  => $user->getRegion('core_id'),
          'id'      => array_map(function($orderProductRelation) { return $orderProductRelation->Product->core_id; }, iterator_to_array($order->ProductRelation)),
          'mode_id' => $order->DeliveryType->core_id,
        ));
      }
      catch (Exception $e) {
        return false;
      }
      $deliveryPrice = isset($r['result']) ? (int)$r['result'] : 0;

      $order->delivery_price = $deliveryPrice;
      $order->delivery_period_id = !empty($data['time_default']) ? $data['time_default'] : null;
      $order->delivered_at = date_format(new DateTime($data['date_default']), 'Y-m-d 00:00:00');
      $order->User = $user->getGuardUser();
      $order->Status = OrderStatusTable::getInstance()->findOneByToken('created');
      $order->sum = $user->getCart()->getTotalForOrder($order) + $deliveryPrice;

      return true;
    };

    foreach ($data as $item)
    {
      $deliveryType = !empty($item['mode_id']) ? DeliveryTypeTable::getInstance()->getByCoreId($item['mode_id']) : null;
      if (!$deliveryType) continue;

      if ('self' == $deliveryType->token)
      {
        foreach ($item['shops'] as $shopData)
        {
          if (!$shopData) continue;

          $shop = ShopTable::getInstance()->getByCoreId($shopData['id']);

          /* @var $order Order */
          $order = clone $baseOrder;

          $order->delivery_type_id = null;
          $order->DeliveryType = $deliveryType;
          $order->Shop = $shop;

          $fillOrderProducts($order, $shopData['products']);
          $fillOrderServices($order, $shopData['products']);

          $fillOrder($order, $item);

          if (count($order->ProductRelation) || count($order->ServiceRelation))
          {
            $orders[] = $order;
          }
        }
      }
      else {
        /* @var $order Order */
        $order = clone $baseOrder;

        $order->delivery_type_id = null;
        $order->DeliveryType = $deliveryType;
        $order->shop_id = null;
        $order->address = null;

        $fillOrderProducts($order, $item['products']);
        $fillOrderServices($order, $item['products']);

        $fillOrder($order, $item);

        if (count($order->ProductRelation) || count($order->ServiceRelation))
        {
          $orders[] = $order;
        }
      }
    }

    //myDebug::dump($orders);
    $coreData = array_map(function($order) { return $order->exportToCore(); }, $orders);
    $response = Core::getInstance()->query('order.create-packet', array(), $coreData, true);

    if (!$response['confirmed'] && isset($response['error']))
    {
      if (isset($response['error']['details']['products']))
      {
        $productIds = array_keys($response['error']['details']['products']);
        $categoryUrl = false;
        if (count($productIds))
        {
          /* @var $product Product */
          $product = ProductTable::getInstance()->getByCoreId($productIds[0]);
          if ($product)
          {
            $categoryUrl = $this->generateUrl('productCatalog_category', $product->getMainCategory());
          }
        }

        $products = array();
        foreach ($productIds as $productId)
        {
          $productId = ProductTable::getInstance()->getIdByCoreId($productId);
          $product = $user->getCart()->getProduct($productId);
          $products[] = array(
            'id'       => $product->core_id,
            'name'     => $product->name,
            'price'    => (int)$product->getRealPrice(),
            'quantity' => $product->cart['quantity'],
          );
        }

        $response['error']['details'] = array(
          'products'     => $products,
          'category_url' => $categoryUrl,
        );
      }

      return array(
        'success' => false,
        'error'   => $response['error'],
      );
    }
    else if ($response['confirmed']) {
      $this->getUser()->getOrder()->setList($orders);

      return array(
        'success' => true,
        //'redirect' => $this->generateUrl($this->order->isOnlinePayment() ? 'order_payment' : 'order_complete'),
        'redirect' => $this->generateUrl('order_complete'),
      );
    }

    return array(
      'success' => false,
      'error'   => array(
        'message' => 'Ошибка при формировании заказа',
      ),
    );
  }

  /**
   *
   * @param int $step
   *
   * @return BaseOrderForm
   */
  protected function getOrderForm($order = null)
  {
    $regionList = RegionTable::getInstance()->getListForOrder($this->getUser()->getCart()->getProducts()->toValueArray('id'));

    // если нет регионов, в которых в наличии все товары в корзине
    if (0 == count($regionList))
    {
      $this->redirect('cart');
    }

    return new OrderDefaultForm(empty($order) ? $this->getUser()->getOrder()->get() : $order, array('user' => $this->getUser(), 'regionList' => $regionList,));
  }

  protected function getNextStep(Order $order)
  {
    $step = 1;

    if (!empty($order->region_id) && (!empty($order->address) || !empty($order->shop_id)))
    {
      $step = 2;
    }

    return $step;
  }

  /**
   *
   * @param type $name
   *
   * @return UnitellerPaymentProvider
   */
  protected function getPaymentProvider($name = null)
  {
    if (null == $name)
    {
      $name = sfConfig::get('app_payment_default_provider');
    }

    $providers = sfConfig::get('app_payment_provider');
    $class = sfInflector::camelize($name . 'payment_provider');

    return new $class($providers[$name]);
  }
}
