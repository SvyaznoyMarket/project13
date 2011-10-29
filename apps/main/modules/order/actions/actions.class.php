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

  public function executeLogin(sfWebRequest $request)
  {
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
    $this->step = $request->getParameter('step', 1);
    $this->order = $this->getUser()->getOrder()->get();

    if (empty($this->order->region_id))
    {
     $this->order->region_id = $this->getUser()->getRegion('id');
     $this->getUser()->getOrder()->set($this->order);
    }

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
          $this->redirect('order_confirm');
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
    );

    $field = $request['field'];
    $this->step = $request->getParameter('step', 1);

    $form = new OrderForm($this->getUser()->getOrder()->get());
    if (isset($form[$field]))
    {
      $form->useFields(array_keys($request->getParameter($form->getName())));
      $form->bind($request->getParameter($form->getName()));

      $order = $form->updateObject();
      $this->getUser()->getOrder()->set($order);

      $this->form = $this->getOrderForm($this->step);

      $result = array(
        'success' => true,
        'data'    => array(
          'content' => isset($renderers[$field]) ? $renderers[$field]($this->form) : $this->getPartial($this->getModuleName().'/field_'.$field, array('form' => $this->form)),
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
    if ($request->isMethod('post'))
    {
      $this->forward($this->getModuleName(), 'create');
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
      $this->getUser()->getOrder()->clear();
    }
    else
    {
      $this->result = $provider->getPaymentResult($this->order);
    }
    $this->getUser()->getCart()->clear();
    $this->getUser()->getOrder()->clear();
    //myDebug::dump($this->order);

    //$this->setVar('order', $this->order, true);
  }
 /**
  * Executes error action
  *
  * @param sfRequest $request A request object
  */
  public function executeError(sfWebRequest $request)
  {
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
      $this->redirect('order_complete');
    }
    else
    {
      $this->redirect('order_error');
    }

  }

  public function executeCallback(sfWebRequest $request)
  {
    $provider = $this->getPaymentProvider();

    $order = $provider->getOrder($request);
    $this->forward404Unless($order);

    $this->result = $provider->getPaymentResult($order);
  }

  protected function saveOrder(Order &$order)
  {
    $order->User = $this->getUser()->getGuardUser();
    $order->sum = $this->getUser()->getCart()->getTotal();
    $order->Status = OrderStatusTable::getInstance()->findOneByToken('created');

    //$this->order->User = UserTable::getInstance()->findOneById($this->getUser()->getGuardUser()->id);//$this->getUser()->getGuardUser();

    foreach ($this->getUser()->getCart()->getProducts() as $product)
    {
      $relation = new OrderProductRelation();
      $relation->fromArray(array(
        'product_id' => $product->id,
        'price'      => $product->price,
        'quantity'   => $product->cart['quantity'],
      ));
      $order->ProductRelation[] = $relation;
    }

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
