<?php

class myUser extends myGuardSecurityUser
{
  protected $cart;

  public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array())
  {
    parent::initialize($dispatcher, $storage, $options);

    $this->cart = new Cart($this->getAttribute('cart', array()));
    $this->productHistory = new UserProductHistory($this->getAttribute('productHistory', array()));
  }

  public function shutdown()
  {
    $this->setAttribute('cart', $this->cart->dump());
    $this->setAttribute('productHistory', $this->productHistory->dump());

    parent::shutdown();
  }

  public function getCart()
  {
    return $this->cart;
  }

  public function getProductHistory()
  {
    return $this->productHistory;
  }

  public function getType()
  {
    return $this->isAuthenticated() ? $this->getGuardUser()->type : null;
  }
}
