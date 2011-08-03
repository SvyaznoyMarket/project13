<?php

class myUser extends myGuardSecurityUser
{
  protected
    $cart = null,
    $productHistory = null,
    $productCompare = null,
    $order = null
  ;

  public function shutdown()
  {
    foreach (array('cart', 'productHistory', 'productCompare', 'order') as $name)
    {
      $object = call_user_func(array($this, 'get'.ucfirst($name)));
      $this->setAttribute($name, $object->dump());
    }

    parent::shutdown();
  }

  public function getCart()
  {
    return $this->getUserData('cart');
  }

  public function getProductHistory()
  {
    return $this->getUserData('productHistory');
  }

  public function getProductCompare()
  {
    return $this->getUserData('productCompare');
  }

  public function getOrder()
  {
    return $this->getUserData('order');
  }

  public function getType()
  {
    return $this->isAuthenticated() ? $this->getGuardUser()->type : null;
  }



  protected function getUserData($name)
  {
    if (null == $this->$name)
    {
      $class = sfInflector::camelize('user_'.$name);
      $this->$name = new $class($this->getAttribute($name, array()));
    }

    return $this->$name;
  }
}
