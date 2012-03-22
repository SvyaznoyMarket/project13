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
    $cart = $this->getUser()->getCart();

    $this->redirectUnless($this->getUser()->getCart()->countFull(), 'cart');

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
        $this->saveOrder($baseOrder, $productData);
      }
    }

    /* @var $region Region */
    $region = $this->getUser()->getRegion('region');

    $getItemsInCart = function($i) { return array('id' => $i['core_id'], 'quantity' => $i['cart']['quantity']); };

    $deliveryMap = $this->getCore()->getDeliveryMap(
      $this->getUser()->getRegion('core_id'),
      array_map($getItemsInCart, $cart->getProducts()->toArray()),
      array_map($getItemsInCart, $cart->getServices()->toArray())
    );

    //$this->setVar('deliveryMap', '{"standart_delayed":{"name":"\u0441\u0442\u0430\u043d\u0434\u0430\u0440\u0442","slug":"standart_delayed","mode_id":1,"delivery_type_id":11,"delivery_id":1,"type":"delivery","price":"290","date_default":"2012-03-18T18:47:21+04:00","date_list":[{"date":"2012-03-18T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-19T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-20T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-21T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-22T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-23T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-24T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-25T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-26T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-27T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-28T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-29T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-30T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-31T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]}],"products":[]},"self":{"name":"\u0441\u0430\u043c\u043e\u0432\u044b\u0432\u043e\u0437","slug":"self","mode_id":3,"delivery_type_id":3,"delivery_id":3,"type":"self","price":"0","date_default":"2012-03-17T18:47:21+04:00","date_list":[{"date":"2012-03-17T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-18T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-19T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-20T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-21T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-22T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-23T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-24T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-25T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-26T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-27T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-28T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-29T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]},{"date":"2012-03-30T00:00:00+04:00","interval":[{"id":21,"time_begin":"10:00","time_end":"21:00"}]}],"shops":[{"id":1,"name":"ENTER - \u041c\u043e\u0441\u043a\u0432\u0430, \u0443\u043b. \u0413\u0440\u0443\u0437\u0438\u043d\u0441\u043a\u0438\u0439 \u0432\u0430\u043b, \u0434. 31 edited","working_time":"\u0441 9.00 \u0434\u043e 21.00","address":"\u0443\u043b. \u0413\u0440\u0443\u0437\u0438\u043d\u0441\u043a\u0438\u0439 \u0412\u0430\u043b, \u0434. 31","coord_long":"37.581675","coord_lat":"55.775004","products":[{"id":2,"name":"\u041a\u0430\u0442\u0430\u043b\u043a\u0430-\u043a\u0430\u0447\u0430\u043b\u043a\u0430 Kiddieland \u00ab\u041f\u043e\u043d\u0438\u00bb","media_image":"http:\/\/core\/upload\/1\/1\/60\/","moveable":true,"price":2260,"quantity":1,"moveto_mode":["standart_delayed","self"],"moveto_shop":[1,2,3]},{"id":305,"name":"\u0424\u0438\u0433\u0443\u0440\u043a\u0430 South Park Butters Talking Wacky Wobbler","media_image":"http:\/\/core\/upload\/1\/1\/60\/6f\/18.jpg","moveable":true,"price":750,"quantity":1,"moveto_mode":["self"],"moveto_shop":[1]}]},{"id":2,"name":"ENTER - \u041c\u043e\u0441\u043a\u0432\u0430, \u0443\u043b. \u041e\u0440\u0434\u0436\u043e\u043d\u0438\u043a\u0438\u0434\u0437\u0435, \u0434. 11","working_time":"\u0441 9.00 \u0434\u043e 21.00","address":"\u041e\u0440\u0434\u0436\u043e\u043d\u0438\u043a\u0438\u0434\u0437\u0435, \u0434. 11, \u0441\u0442\u0440. 10","coord_long":"37.596997","coord_lat":"55.706488","products":[{"id":3,"name":"\u0429\u0435\u0442\u043a\u0430 \u0441 \u0440\u0435\u0437\u0435\u0440\u0432\u0443\u0430\u0440\u043e\u043c \u0434\u043b\u044f \u043c\u044b\u0442\u044c\u044f \u043f\u043e\u0441\u0443\u0434\u044b  Rozenbal","media_image":"a6\/54.jpg","moveable":true,"price":149,"quantity":1,"moveto_mode":["self"],"moveto_shop":[2,3]}]},{"id":3,"name":"ENTER - \u041c\u043e\u0441\u043a\u0432\u0430, \u0443\u043b. \u0411. \u0414\u043e\u0440\u043e\u0433\u043e\u043c\u0438\u043b\u043e\u0432\u0441\u043a\u0430\u044f, \u0434. 8","working_time":"\u0441 9.00 \u0434\u043e 21.00","address":"\u0443\u043b. \u0411. \u0414\u043e\u0440\u043e\u0433\u043e\u043c\u0438\u043b\u043e\u0432\u0441\u043a\u0430\u044f, \u0434. 8","coord_long":"37.565389","coord_lat":"55.746197","products":[]}]},"standart_rapid":{"name":"\u0441\u0442\u0430\u043d\u0434\u0430\u0440\u0442","slug":"standart_rapid","mode_id":1,"delivery_type_id":11,"delivery_id":2,"type":"delivery","price":"700","date_default":"2012-03-16T18:47:21+04:00","date_list":[{"date":"2012-03-16T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-17T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-18T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-19T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-20T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-21T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-22T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-23T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-24T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-25T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-26T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-27T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-28T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]},{"date":"2012-03-29T00:00:00+04:00","interval":[{"id":2,"time_begin":"09:00","time_end":"14:00"},{"id":5,"time_begin":"09:00","time_end":"18:00"},{"id":3,"time_begin":"14:00","time_end":"18:00"},{"id":4,"time_begin":"18:00","time_end":"21:00"}]}],"products":[{"id":2494,"name":"\u041a\u0443\u043f\u043e\u043b\u044c\u043d\u0430\u044f \u0432\u044b\u0442\u044f\u0436\u043a\u0430 Krona Stella smart 900 5P","media_image":"80\/6100.jpg","moveable":true,"price":23590,"quantity":1,"moveto_mode":["self","standart_rapid"],"moveto_shop":[1,2,3]}]}}');
    $this->setVar('deliveryMap', json_encode($deliveryMap), true);
    $this->setVar('mapCenter', json_encode(array('latitude' => $region->getLatitude(), 'longitude' => $region->getLongitude())));
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
    //$cart = $this->getUser()->getCart();
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

  // TODO: удалить
  public function executeLogin(sfWebRequest $request)
  {
    $this->getResponse()->setTitle('Данные покупателя – Enter.ru');

    if (!$this->getUser()->getCart()->count())
    {
      $this->redirect($this->getUser()->getReferer());
    }
    if (!$this->getUser()->isAuthenticated())
    {
      $this->formSignin = new UserFormSignin();
      $this->formRegister = new UserFormRegister();
      $action = $request->hasParameter($this->formRegister->getName()) ? 'register' : 'login';
    } else
    {
      $this->redirect('order_new');
    }

    if ($request->isMethod('post') && isset($action))
    {
      switch ($action)
      {
        case 'login':
          $this->formSignin->bind($request->getParameter($this->formSignin->getName()));
          if ($this->formSignin->isValid())
          {
            $values = $this->formSignin->getValues();
            $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);
            $this->redirect('order_new');

            // always redirect to a URL set in app.yml
            // or to the referer
            // or to the homepage
          }
          break;
        case 'register':
          $this->formRegister->bind($request->getParameter($this->formRegister->getName()));

          if ($this->formRegister->isValid())
          {
            $user = $this->formRegister->getObject();

            $user->is_active = true;
            $user->email = $this->formRegister->getValue('email');
            $user->phonenumber = $this->formRegister->getValue('phonenumber');
            $user->region_id = $this->getUser()->getRegion('id');

            //$user->setPassword('123456');

            try
            {
              $user = $this->formRegister->save();
              $this->getUser()->signIn($user);
              $this->redirect('order_new');
            } catch (Exception $e)
            {
              $this->getLogger()->err('{' . __CLASS__ . '} ' . $e->getMessage());
            }
            //$user->refresh();
          }
          break;
      }
      //myDebug::dump($request->getParameter('action'));
      $this->setVar('action', $action);
    }
    //$this->order = $this->getUser()->getOrder()->get();
  }

  /**
   *
   * @param Order $order
   *
   * @return bool
   */
  protected function saveOrder(Order $baseOrder, array $data)
  {
    $guardUser = $this->getUser()->getGuardUser();
    $cart = $this->getUser()->getCart();

    $deliveryPrices = $cart->getDeliveriesPrice();

    $fillOrderProducts = function(Order $order, $data) use ($cart)
    {
      /* @var $cart UserCart */

      foreach ($data as $productData)
      {
        if (empty($productData['is_service']))
        {
          $product_id = ProductTable::getInstance()->getIdByCoreId($productData['id']);

          /* @var $product Product */
          $product = $cart->getProduct($product_id);
          if (!$product) continue;

          $relation = new OrderProductRelation();
          $relation->setProduct($product);
          $relation->setPrice(ProductTable::getInstance()->getRealPrice($product));
          $relation->setQuantity($product->cart['quantity']);

          $order->ProductRelation[] = $relation;
        }
      }
    };

    $fillOrderServices = function(Order $order, $data) use ($cart)
    {
      /* @var $cart UserCart */

      foreach ($data as $serviceData)
      {
        if (!empty($serviceData['is_service']))
        {
          $service_id = ServiceTable::getInstance()->getIdByCoreId($serviceData['id']);

          /* @var $service Service */
          $service = $cart->getService($service_id);
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

    myDebug::dump($deliveryPrices);
    $fillOrder = function(Order $order, array $data) use ($cart, $guardUser, $deliveryPrices) {
      /* @var $cart UserCart */

      $deliveryPrice = isset($deliveryPrices[$order->delivery_type_id]) ? $deliveryPrices[$order->delivery_type_id] : 0;

      $order->delivery_price = $deliveryPrice;
      $order->delivery_period_id = !empty($data['time_default']) ? $data['time_default'] : null;
      $order->delivered_at = date_format(new DateTime($data['date_default']), 'Y-m-d 00:00:00');
      $order->User = $guardUser;
      $order->Status = OrderStatusTable::getInstance()->findOneByToken('created');
      $order->sum = $cart->getTotalForOrder($order)/* + $deliveryPrice*/;
    };

    $orderData = array();
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

          $order->delivery_type_id = $deliveryType->id;
          $order->Shop = $shop;

          $fillOrderProducts($order, $shopData['products']);
          $fillOrderServices($order, $shopData['products']);

          $fillOrder($order, $item);

          if (count($order->ProductRelation) || count($order->ServiceRelation))
          {
            $orders[] = $order;
            myDebug::dump(array($deliveryType->token.'-'.$deliveryType->id => $order->sum));
          }
        }
      }
      else {
        /* @var $order Order */
        $order = clone $baseOrder;

        $order->delivery_type_id = $deliveryType->id;
        $order->shop_id = null;
        $order->address = null;

        $fillOrderProducts($order, $item['products']);
        $fillOrderServices($order, $item['products']);

        $fillOrder($order, $item);

        if (count($order->ProductRelation) || count($order->ServiceRelation))
        {
          $orders[] = $order;
          myDebug::dump(array($deliveryType->token.'-'.$deliveryType->id => $order->sum));
        }
      }
    }

    //myDebug::dump($orderData, 1);
    exit();

    try
    {
      $order->save();

      //$this->order->update
      $this->getUser()->getOrder()->set($order);
      //$this->getUser()->getCart()->clear();
      return true;
    } catch (Exception $e)
    {
      $this->getLogger()->err('{' . __CLASS__ . '} create: can\'t save to core: ' . $e->getMessage());
    }

    return false;
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
