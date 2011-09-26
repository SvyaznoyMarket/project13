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
  const LAST_STEP = 2;

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
  * Executes new action
  *
  * @param sfRequest $request A request object
  */
  public function executeNew(sfWebRequest $request)
  {
    $this->step = $request->getParameter('step', 1);
    $this->order = $this->getUser()->getOrder()->get();

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
          'content' => $this->form[$field]->renderRow(),
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
  * Executes confirm action
  *
  * @param sfRequest $request A request object
  */
  public function executeConfirm(sfWebRequest $request)
  {
    $this->order = $this->getUser()->getOrder()->get();

    if ($request->isMethod('post'))
    {
      $this->forward($this->getModuleName(), 'create');
    }
  }
 /**
  * Executes complete action
  *
  * @param sfRequest $request A request object
  */
  public function executeComplete(sfWebRequest $request)
  {
    $this->order = $this->getUser()->getOrder()->get();
  }
 /**
  * Executes create action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreate(sfWebRequest $request)
  {
    $this->order = $this->getUser()->getOrder()->get();

    foreach ($this->getUser()->getCart()->getProducts() as $product)
    {
      $relation = new OrderProductRelation();
      $relation->fromArray(array(
        'product_id' => $product->id,
        'price'      => $product->price,
        'quantity'   => $product->cart['quantity'],
      ));
      $this->order->ProductRelation[] = $relation;
    }
    //myDebug::dump($this->order, 1);
    $this->order->save();

    $this->redirect('order_complete');
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

}
