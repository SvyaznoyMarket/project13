<?php

/**
 * productStock components.
 *
 * @package    enter
 * @subpackage productStock
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productStockComponents extends myComponents
{
 /**
  * Executes show component
  *
  * @param product $product Товар
  */
  public function executeShow()
  {
    $list = array();
    foreach ($this->product->getShopList() as $shop)
    {
      $list[] = array(
        'name'     => $shop->name,
        'token'    => $shop->token,
        'url'      => url_for('shop_show', $shop),
        'quantity' => $shop->quantity,
      );
    }

    $this->setVar('list', $list, true);
  }
}

