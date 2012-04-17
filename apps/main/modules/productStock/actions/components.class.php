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

    $quantityInRegion = $this->product->getStockQuantity() ?: 0; //1421

    $shopList = $this->product->getShopList(array('region_id' => $region['id']));
    // добавляет к каждому магазину количество товара на складе региона
    // удаляет магазины, в которых нет товара
    foreach ($shopList as $i => $shop)
    {
      //$shop['product_quantity'] += $quantityInRegion; //1421

      if (0 == $shop['product_quantity'])
      {
        unset($shopList[$i]);
      }
    }

    $markers = array();
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

