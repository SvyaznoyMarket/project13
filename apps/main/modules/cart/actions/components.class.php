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
    $this->disable = false;
    $productPath = '';
    if (is_object($this->product)) {
        if (!$this->product->is_insale)
        {
            $this->disable = true;
        }
        $productId = $this->product->id;
        if (property_exists($this->product, 'path')) {
            $productPath = $this->product->token;
        }
    } else {
        if (!$this->product['is_insale'])
        {
            $this->disable = true;
        }
        $productId = $this->product['id'];
        $productPath = $this->product['token'];
    }
    $hasProduct = $cart->hasProduct($productId);
    $this->setVar('productPath', $productPath);
    $this->setVar('productId', $productId);



    if ($hasProduct)
    {
      $this->button = 'cart';
    }
    else
    {
      $this->button = 'buy';
    }
      //myDebug::dump($this->button);

    if ($this->soa == true) {
        $this->view = 'soa';
    } elseif (!in_array($this->view, array('default', 'delivery'))) {
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
      $orderArticle = $this->getUser()->getCart()->getSeoCartArticle();
      $this->setVar('orderArticle', $orderArticle, true);
  }

}
