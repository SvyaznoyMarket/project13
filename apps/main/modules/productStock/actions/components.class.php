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
    $region = $this->getUser()->getRegion('region');

    $shopList = $this->product->getShopList(array(
      'region_id' => $region['id'],
    ));

    foreach ($shopList as $shop)
    {
      $markers[$shop->id] = array(
        'id'        => $shop->id,
        'region_id' => $shop->region_id,
        'url'       => $this->generateUrl('order_1click', array('product' => $this->product['barcode'], 'shop' => $shop->token)),
        'name'      => $shop->name,
        'address'   => $shop->address,
        'regime'    => $shop->regime,
        'latitude'  => $shop->latitude,
        'longitude' => $shop->longitude,
      );
    }

    $this->setVar('region', $region, true);
    $this->setVar('shopList', $shopList, true);
    $this->setVar('markers', $markers, true);
  }
}

