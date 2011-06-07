<?php

class myUser extends myGuardSecurityUser
{
  protected $cart;

  public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array())
  {
    parent::initialize($dispatcher, $storage, $options);

    $cart = $this->getAttribute('cart', array());
    $this->cart = new Cart($cart);
  }

  public function shutdown()
  {
    $this->setAttribute('cart', $this->cart->dump());

    parent::shutdown();
  }

  public function getCart()
  {
    return $this->cart;
  }

  public function getType()
  {
    return $this->isAuthenticated() ? $this->getGuardUser()->type : null;
  }

}
