<?php

/**
 * payment actions.
 *
 * @package    enter
 * @subpackage payment
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class paymentActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $provider = $this->getPaymentProvider();

    $order = new Order();
    $order->fromArray(array(
      'sum' => 1500,
    ));
    $order->save();
    $this->form = $provider->getForm($order);
  }
 /**
  * Executes callback action
  *
  * @param sfRequest $request A request object
  */
  public function executeCallback(sfWebRequest $request)
  {
    $provider = $this->getPaymentProvider();

    $order = $provider->getOrder($request);
    $this->forward404Unless($order);

    $this->result = $provider->getPaymentResult($order);
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
