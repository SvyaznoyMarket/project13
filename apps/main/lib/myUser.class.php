<?php

class myUser extends myGuardSecurityUser
{
  protected
    $cart = null,
    $productHistory = null
  ;

  public function shutdown()
  {
    $this->setAttribute('cart', $this->getCart()->dump());
    $this->setAttribute('productHistory', $this->getProductHistory()->dump());

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

  public function getType()
  {
    return $this->isAuthenticated() ? $this->getGuardUser()->type : null;
  }
}
