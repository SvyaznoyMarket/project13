<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 10.05.12
 * Time: 15:48
 * To change this template use File | Settings | File Templates.
 */
return array(
  array('/product/delivery1click'            , 'delivery.ProductDeliveryJson'),
  array('/product/delivery-info'             , 'delivery.ProductListShortDeliveryJson'),
  array('/product/delivery-info/<productId>' , 'delivery.ProductListShortDeliveryJson'),
  array('/category/main_menu'                , 'catalog.MainMenu'),
  array('/region/init'                       , 'region.getShopAvailable')
);