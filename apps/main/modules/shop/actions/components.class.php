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
    if (!in_array($this->view, array('default', 'inlist')))
    {
      $this->view = 'default';
    }

    $item = array(
      'name'         => (string)$this->shop,
      'address'      => $this->shop->address,
      'phonenumbers' => $this->shop->phonenumbers,
      'regime'       => $this->shop->regime,
      'description'  => $this->shop->description,
      'way_walk'     => $this->shop->way_walk,
      'way_auto'     => $this->shop->way_auto,
      'latitude'     => $this->shop->latitude,
      'longitude'    => $this->shop->longitude,
      'photos'       => array(),
    );


    foreach ($this->shop->Photo as $shopPhoto)
    {
      $item['photos'][] = array(
        'url_small' => $shopPhoto->getPhotoUrl(5), // 1
        'url_big'   => $shopPhoto->getPhotoUrl(5), // 4
      );
    }

    $this->setVar('item', $item, true);
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

