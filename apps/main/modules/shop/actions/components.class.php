<?php

/**
 * shop components.
 *
 * @package    enter
 * @subpackage shop
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class shopComponents extends myComponents
{
 /**
  * Executes show component
  *
  * @param Shop $shop Магазин
  */
  public function executeShow()
  {
    $this->item = array(
      'name'         => (string)$this->shop,
      'address'      => $this->shop->address,
      'phonenumbers' => $this->shop->phonenumbers,
      'regime'       => $this->shop->regime,
      'description'  => $this->shop->description,
    );
  }
 /**
  * Executes list component
  *
  * @param Region $region Регион
  */
  public function executeList()
  {
    $list = array();
    foreach (ShopTable::getInstance()->getList() as $shop)
    {
      $list[] = array(
        'name'         => (string)$shop,
        'token'        => $shop->token,
        'phonenumbers' => $shop->phonenumbers,
        'description'  => $shop->description,
        'url'          => url_for('shop_show', $shop),
      );
    }

    $this->setVar('list', $list, true);
  }
}

