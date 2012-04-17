<?php

/**
 * creator components.
 *
 * @package    enter
 * @subpackage cart
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @property ProductEntity $product
 * @property int $quantity Количество товара
 * @property string $view Вид отображения кнопки
 * @property boolean $disable Вид отображения кнопки
 * @property string $button Вид отображения кнопки
 */
class cart_Components extends myComponents
{
  /**
   * Executes buy_button component
   *
   * @internal param Product $product Товар
   * @internal param int $quantity Количество товара
   * @internal param string $view Вид отображения кнопки
   */
  public function executeBuy_button()
  {
    if (empty($this->quantity)) {
      $this->quantity = 1;
    }
    /** @var $user myUser */
    $user = $this->getUser();
    /** @var $cart UserCart */
    $cart = $user->getCart();
    $this->disable = false;

    $this->setVar('productPath', $this->product->getToken());
    $this->setVar('productId', $this->product->getId());

    $this->button = $cart->hasProduct($this->product->getId()) ? 'cart' : 'buy';


    if (!in_array($this->view, array('add', 'default'), true)) {
      $this->view = 'default';
    }
  }
}
