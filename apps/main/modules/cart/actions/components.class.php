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

    $cart = $this->getUser()->getCart();

    if (!$this->product->is_insale)
    {
      return sfView::NONE;
    }

    if ($cart->hasProduct($this->product->id))
    {
      $this->button = 'cart';
    }
    else
    {
      $this->button = 'buy';
    }

    if (!in_array($this->view, array()))
    {
      $this->view = 'default';
    }
  }
 /**
  * Executes list component
  *
  */
  public function executeList()
  {
    $cart = $this->getUser()->getCart();

    $list = array();
    foreach ($cart->getProducts() as $product)
    {
      $services = $product->getServiceList();
      $service_for_list = array();
      foreach ($services as $service)
      {
        $service_for_list[$service->token] = array(
          'name'      => $service->name,
          'token'     => $service->token,
          'quantity'  => isset($product['cart']['service'][$service->id]['quantity']) ? $product['cart']['service'][$service->id]['quantity'] : 0,
        );
      }

      $list[] = array(
        'token'     => $product->token,
        'name'      => (string)$product,
        'quantity'  => $product['cart']['quantity'],
        'service'   => $service_for_list,
      );
    }
    myDebug::dump($list);
    $this->setVar('list', $list, true);
  }
}
