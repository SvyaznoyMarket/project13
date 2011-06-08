<?php

/**
 * creator components.
 *
 * @package    enter
 * @subpackage cart
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class cartComponents extends myComponents
{
 /**
  * Executes buy_button component
  *
  * @param Product $product Товар
  * @param int $amount Количество товара
  * @param string $view Вид отображения кнопки
  */
  public function executeBuy_button()
  {
    $cart = $this->getUser()->getCart();

    if ($cart->hasProduct($this->product->id))
    {
      $this->button = 'cart';
    }
    else
    {
      $this->button = 'buy';
    }
    if (!isset($this->view) || is_null($this->view))
    {
      $this->view = 'default';
    }
  }
}
