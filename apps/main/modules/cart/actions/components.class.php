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
  * @param int $quantity Количество товара
  * @param string $view Вид отображения кнопки
  */
  public function executeBuy_button()
  {
    if (empty($this->quantity))
    {
      $this->quantity = 1;
    }

    $this->disable = false;
    if (!$this->product['is_insale'])
    {
      $this->disable = true;
    }

    $cart = $this->getUser()->getCart();

    if ($cart->hasProduct($this->product['id']))
    {
      $this->button = 'cart';
    }
    else
    {
      $this->button = 'buy';
    }

    if (!in_array($this->view, array('default', 'delivery')))
    {
      $this->view = 'default';
    }
  }
 /**
  * Executes show component
  *
  */
  public function executeShow()
  {
    if (!in_array($this->view, array('default', 'order')))
    {
      $this->view = 'default';
    }
    $cart = $this->getUser()->getCart();

    if ($this->view == 'order')
    {
      $list = $cart->getReceiptList();
    }
    else
    {
      $list = $cart->getProductServiceList(true);
    }

    $this->setVar('list', $list, true);
  }

  public function executeSeo_counters_advance()
  {
  }

}
