<?php

class UserOrder extends BaseUserData
{
  function __construct($parameters = array())
  {
    $parameters = myToolkit::arrayDeepMerge(array('order' => array(), ), $parameters);
    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add($parameters);
  }

  public function get()
  {
    $order = new Order();
    $order->fromArray($this->parameterHolder->get('order', array()));

    return $order;
  }

  public function set(Order $order)
  {
    $this->parameterHolder->set('order', $order->toArray());
  }
}