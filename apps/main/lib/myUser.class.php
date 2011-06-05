<?php

class myUser extends myGuardSecurityUser
{
  protected
    $cart = null,
    $productHistory = null,
    $productCompare = null
  ;

  public function shutdown()
  {
    foreach (array('cart', 'productHistory', 'productCompare') as $name)
    {
      $object = call_user_func(array($this, 'get'.ucfirst($name)));
      $this->setAttribute($name, $object->dump());
    }

    parent::shutdown();
  }

  public function getCart()
  {
    if (null == $this->cart)
    {
      $this->cart = new Cart($this->getAttribute('cart', array()));
    }

    return $this->cart;
  }

  public function getProductHistory()
  {
    if (null == $this->productHistory)
    {
      $this->productHistory = new UserProductHistory($this->getAttribute('productHistory', array()));
    }

    return $this->productHistory;
  }

  public function getProductCompare()
  {
    if (null == $this->productCompare)
    {
      $this->productCompare = new UserProductCompare($this->getAttribute('productCompare', array()));
    }

    return $this->productCompare;
  }

  public function getType()
  {
    return $this->isAuthenticated() ? $this->getGuardUser()->type : null;
  }
}
