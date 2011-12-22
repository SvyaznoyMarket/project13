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
      $this->redirect($request->getReferer().'#order1click-link');
    }

    $return = array(
      'success' => false,
    );

    $this->product = ProductTable::getInstance()->getByBarcode($request->getParameter('product'), array('with_model' => true));

    $this->order = new Order();
    $this->order->User = $this->getUser()->getGuardUser();
    $this->order->Status = OrderStatusTable::getInstance()->findOneByToken('created');
    $this->order->PaymentMethod = PaymentMethodTable::getInstance()->findOneByToken('nalichnie');
    $this->order->delivery_type_id = 1;
    $this->order->sum = ProductTable::getInstance()->getRealPrice($this->product); //нужна для правильного отбражения формы заказа

    if (empty($this->order->region_id))
    {
      $this->order->region_id = $this->getUser()->getRegion('id');
    }

    $this->form = new OrderOneClickForm($this->order, array('user' => $this->getUser()->getGuardUser()));
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $order = $this->form->updateObject();

        if ($this->product->isKit())
        {
          foreach ($this->product->PartRelation as $partRelation)
          {
            $part = ProductTable::getInstance()->getById($partRelation->part_id, array('with_model' => true));

            $relation = new OrderProductRelation();
            $relation->fromArray(array(
              'product_id' => $part['id'],
              'price'      => ProductTable::getInstance()->getRealPrice($part),
              'quantity'   => $this->form->getValue('product_quantity'),
            ));
            $order->ProductRelation[] = $relation;
          }
        }
        else {
          $relation = new OrderProductRelation();
          $relation->fromArray(array(
            'product_id' => $this->product->id,
            'price'      => ProductTable::getInstance()->getRealPrice($this->product),
            'quantity'   => $this->form->getValue('product_quantity'),
          ));
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
          $order->payment_details = 'Это быстрый заказ за 1 клик. Уточните параметры заказа у клиента.';
          $order->save();

          $form = new UserFormSilentRegister();
          $form->bind(array(
            'username'   => $order->recipient_phonenumbers,
            'first_name' => trim($order->recipient_first_name.' '.$order->recipient_last_name),
          ));

          $return['success'] = true;
          $return['message'] = 'Заказ успешно создан';
          $return['data'] = array(
            'title'   => 'Ваш заказ принят, спасибо!',
            'content' => $this->getPartial($this->getModuleName().'/complete', array('order' => $order, 'form' => $form)),
          );
        }
        catch (Exception $e)
        {
          $return['message'] = 'Не удалось создать заказ'.(sfConfig::get('sf_debug') ? (' Ошибка: '.$e->getMessage()) : '');
        }
      }
      else {
        $return = array(
          'success' => false,
          'data'    => array(
            'form' => $this->getPartial($this->getModuleName().'/form_oneClick'),
          ),
        );
      }

      return $this->renderJson($return);
    }

    return $this->renderJson(array(
      'success' => true,
      'data'    => array(
        'form' => $this->getPartial($this->getModuleName().'/form_oneClick'),
      ),
    ));
  }

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
	  }
    else
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
            }
            catch (Exception $e)
            {
              $this->getLogger()->err('{'.__CLASS__.'} '.$e->getMessage());
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
  * Executes new action
  *
  * @param sfRequest $request A request object
  */
  public function executeNew(sfWebRequest $request)
  {
    $this->redirectUnless($this->getUser()->getCart()->countFull(), 'cart');

    $this->getResponse()->setTitle('Способ доставки и оплаты  – Enter.ru');

    $this->step = $request->getParameter('step', 1);
    $this->order = $this->getUser()->getOrder()->get();

    $this->order->region_id = $this->getUser()->getRegion('id');
    $this->getUser()->getOrder()->set($this->order);

    $this->form = $this->getOrderForm($this->step);
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $order = $this->form->updateObject();
        $order->step = self::LAST_STEP == $this->step ? (self::LAST_STEP + 1) : $this->step;
        $this->getUser()->getOrder()->set($order);

        if (self::LAST_STEP == $this->step)
        {
          $this->redirect('order_create');
        }
        else {
          $this->redirect('order_new', array('step' => $this->getNextStep($order)));
        }
      }
      else
      {
        //myDebug::dump($this->form['region_id']->getValue());
        //myDebug::dump($this->form->getValues());
        //myDebug::dump($this->form['region_id']->getValue(), 1);
        //$order = $this->form->updateObject(array($this->form['region_id'], ));
        //$this->getUser()->getOrder()->set($order);
      }
    }
  }
 /**
  * Executes updateField action
  *
  * @param sfRequest $request A request object
  */
  public function executeUpdateField(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $renderers = array(
      'delivery_period_id'  => function($form) {
        return myToolkit::arrayDeepMerge(array('' => ''), $form['delivery_period_id']->getWidget()->getChoices());
      },
      'delivered_at'  => function($form) {
        return myToolkit::arrayDeepMerge(array('' => ''), $form['delivered_at']->getWidget()->getChoices());
      },
    );

    $field = $request['field'];
    $this->step = $request->getParameter('step', 1);

    $form = new OrderStep1Form($this->getUser()->getOrder()->get());
    if (isset($form[$field]))
    {
      //$form->useFields(array($field) + array_keys($request->getParameter($form->getName())));
      $form->bind($request->getParameter($form->getName()));

      $order = $form->updateObject();
      $this->getUser()->getOrder()->set($order);

      $result = array(
        'success' => true,
        'data'    => array(
          'content' => isset($renderers[$field]) ? $renderers[$field]($form) : $this->getPartial($this->getModuleName().'/field_'.$field, array('form' => $form)),
        ),
      );
    }
    else {
      $result = array(
        'success' => false,
      );
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
      if (!$token)     $this->redirect($this->getRequest()->getReferer());

      $orderL = OrderTable::getInstance()->findBy('token', $token);
      foreach($orderL as $order) $coreId = $order->core_id;
      //print_r($order->getData());
      $res = Core::getInstance()->query('order.cancel',array('id'=>$coreId));
      //если отменилось на ядре, отменим здесь тоже
      if ($res){
          //$order->setData( array('status_id'=>Order::STATUS_CANCELLED));
          //->set('status_id', Order::STATUS_CANCELLED);
          $order->setCorePush(false);
          $order->setArray(array('status_id'=>Order::STATUS_CANCELLED));
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
      if ($this->saveOrder($order))
      {
        $provider = $this->getPaymentProvider();
        $this->paymentForm = $provider->getForm($order);
      }
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
    }
    else
    {
      $this->result = $provider->getPaymentResult($this->order);
    }

    //$this->redirectUnless($this->order->exists(), 'order_new');

    $this->form = new UserFormSilentRegister();
    $this->form->bind(array(
      'username'   => $this->order->recipient_phonenumbers,
      'first_name' => trim($this->order->recipient_first_name.' '.$this->order->recipient_last_name),
    ));

    if (!$this->form->isValid())
    {
      $this->form = new UserFormBasicRegister(null, array('validate_username' => false));
      $this->form->bind(array(
        'first_name' => trim($this->order->recipient_first_name.' '.$this->order->recipient_last_name),
      ));
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

    $form = new OrderStep1Form();

    return $this->renderJson(array(
      'success' => $this->getUser()->isAuthenticated(),
      'data'    => array(
        'content' => $this->getPartial($this->getModuleName().'/user'),
        'fields'  =>
          $user
          ? array(
            $form['recipient_first_name']->renderName()   => $user->first_name,
            $form['recipient_last_name']->renderName()    => $user->last_name,
            $form['recipient_phonenumbers']->renderName() => $user->phonenumber,
          )
          : false,
      ),
    ));
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
    }
    else
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
   * @return bool
   */
  protected function saveOrder(Order &$order)
  {
    $deliveryPrices = $this->getUser()->getCart()->getDeliveriesPrice();
    $deliveryPrice = isset($deliveryPrices[$order->delivery_type_id]) ? $deliveryPrices[$order->delivery_type_id] : 0;
    $order->User = $this->getUser()->getGuardUser();
    $order->sum = $this->getUser()->getCart()->getTotal() + $deliveryPrice;
    $order->Status = OrderStatusTable::getInstance()->findOneByToken('created');

    //$this->order->User = UserTable::getInstance()->findOneById($this->getUser()->getGuardUser()->id);//$this->getUser()->getGuardUser();


    foreach ($this->getUser()->getCart()->getProducts() as $product)
    {
      $relation = new OrderProductRelation();
      $relation->fromArray(array(
        'product_id' => $product->id,
        'price'      => ProductTable::getInstance()->getRealPrice($product),
        'quantity'   => $product->cart['quantity'],
      ));
      $order->ProductRelation[] = $relation;
    }

    foreach ($this->getUser()->getCart()->getServices() as $service)
    {
      if ($service->cart['quantity'] > 0) {
          $relation = new OrderServiceRelation();
          $relation->fromArray(array(
            'service_id' => $service->id,
            'price'      => $service->price,
            'quantity'   => $service->cart['quantity'],
          ));
          $order->ServiceRelation[] = $relation;
      }
      if (count($service->cart['product']) > 0) {
          foreach($service->cart['product'] as $prodId => $qty) {
              if (!$prodId || !$qty) continue;
              $relation = new OrderServiceRelation();
              $relation->fromArray(array(
                'service_id' => $service->id,
                'product_id' => $prodId,
                'price'      => $service->price,
                'quantity'   => $qty,
              ));
              $order->ServiceRelation[] = $relation;
          }
      }
    }

    /*
    foreach ($this->getUser()->getCart()->getProductServiceList() as $product)
    {
        $relation = new OrderProductRelation();
        $relation->fromArray(array(
            'product_id' => $product['id'],
            'price'      => $product['price'],
            'quantity'   => $product['quantity'],
        ));
        $order->ProductRelation[] = $relation;

        foreach ($product['service'] as $service)
        {
            $relation = new OrderServiceRelation();
            $relation->fromArray(array(
                'product_id' => $product['id'],
                'service_id' => $service['id'],
                'price'      => $service['price'],
                'quantity'   => $service['quantity'],
            ));
            $order->ServiceRelation[] = $relation;
        }
    } */

    try
    {
      $order->save();

      //$this->order->update
      $this->getUser()->getOrder()->set($order);
      //$this->getUser()->getCart()->clear();
      return true;
    }
    catch (Exception $e)
    {
      $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save to core: '.$e->getMessage());
    }

    return false;
  }

  /**
   *
   * @param int $step
   * @return BaseOrderForm
   */
  protected function getOrderForm($step)
  {
    $class = sfInflector::camelize("order_step_{$step}_form");
    $this->forward404Unless(!empty($step) && class_exists($class), 'Invalid order step');
    return new $class($this->getUser()->getOrder()->get());
  }

  protected function getNextStep(Order $order)
  {
    $step = 1;

    if (true
      && !empty($order->region_id)
      && (!empty($order->address) || !empty($order->shop_id))
    ) {
      $step = 2;
    }

    return $step;
  }

  /**
   *
   * @param type $name
   * @return UnitellerPaymentProvider
   */
  protected function getPaymentProvider($name = null)
  {
    if (null == $name)
    {
      $name = sfConfig::get('app_payment_default_provider');
    }

    $providers = sfConfig::get('app_payment_provider');
    $class = sfInflector::camelize($name.'payment_provider');

    return new $class($providers[$name]);
  }
}
