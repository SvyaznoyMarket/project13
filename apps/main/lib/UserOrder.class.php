<?php

class UserOrder extends BaseUserData
{
  function __construct($parameters = array())
  {
    $parameters = myToolkit::arrayDeepMerge(array('order' => array(),), $parameters);
    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add($parameters);
  }

  public function get()
  {
    $order = $this->parameterHolder->get('order', array());
    //$order = new Order();
    //$order->fromArray($this->parameterHolder->get('order', array()));

    if (isset($order['id']) && !empty($order['id'])) {
      $result = OrderTable::getInstance()->getById($order['id']);
    }
    else
    {
      $result = new Order();
      $result->fromArray($order);
    }

    return $result;
  }

  public function set(Order $order)
  {
    $this->parameterHolder->set('order', $order->toArray(false));
  }

  public function clear()
  {
    $this->parameterHolder->set('order', array());
  }
}
