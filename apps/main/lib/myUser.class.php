<?php

class myUser extends sfBasicSecurityUser
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


}
