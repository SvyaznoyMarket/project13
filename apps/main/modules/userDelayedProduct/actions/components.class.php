<?php

/**
 * userDelayedProduct components.
 *
 * @package    enter
 * @subpackage userDelayedProduct
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userDelayedProductComponents extends myComponents
{
 /**
  * Executes list component
  *
  */
  public function executeList()
  {
    $list = array();
    foreach ($this->getUser()->getGuardUser()->getDelayedProduct() as $userDelayedProduct)
    {
      $product = $userDelayedProduct->Product;
      $list[] = array(
        'name'    => (string)$product,
        'product' => $product,
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes add_button component
  *
  * @param Product $product Товар
  *
  */
  public function executeAdd_button()
  {
    $this->button = 'add';

    foreach ($this->getUser()->getGuardUser()->getDelayedProduct() as $userDelayedProduct)
    {
      if ($this->product->id != $userDelayedProduct->product_id) continue;

      $this->button = 'show';
      break;
    }

    if (!in_array($this->view, array()))
    {
      $this->view = 'default';
    }
  }
}
