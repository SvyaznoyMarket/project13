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

    $values = $this->product->getShopList()->toKeyValueArray('id', 'quantity');

    foreach (ShopTable::getInstance()->getList() as $shop)
    {
      $index = $shop->region_id;

      if (!isset($list[$index]))
      {
        $list[$index] = array(
          'name'  => $shop->Region->name,
          'shops' => array(),
        );
      }

      $list[$index]['shops'][] = array(
        'name'     => $shop->name,
        'token'    => $shop->token,
        'url'      => $this->generateUrl('shop_show', $shop),
        'quantity' => isset($values[$shop->id]) ? $values[$shop->id] : 0,
      );
    }

    $this->setVar('list', $list, true);
  }
}

