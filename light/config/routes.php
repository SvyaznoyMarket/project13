<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 10.05.12
 * Time: 15:48
 * To change this template use File | Settings | File Templates.
 */
return array(
  array('/product/<productToken>'            , 'product.show'), //@TODO реализовать, пока для генерации урлов
  array('/catalog/<categoryToken>/'          , 'catalog.showCategory'), //@TODO реализовать, пока для генерации урлов
  array('/products/set/<productBarcodeList>' , 'product.set'), //@TODO реализовать, пока для генерации урлов
  array('/search'                            , 'search.form'), //@TODO реализовать, пока для генерации урлов
  array('/shops/<regionToken>'               , 'shop.regionList'), //@TODO реализовать, пока для генерации урлов
  array('/private/'                          , 'user.index'), //@TODO реализовать, пока для генерации урлов
  array('/product/delivery1click'            , 'delivery.ProductDeliveryJson'),
  array('/product/delivery-info'             , 'delivery.ProductListShortDeliveryJson'),
  array('/product/delivery-info/<productId>' , 'delivery.ProductListShortDeliveryJson'),
  array('/category/main_menu'                , 'catalog.MainMenu'),
  array('/region/init'                       , 'region.getShopAvailable'),
  array('/'                                  , 'staticPage.mainPage'),
  array('/<pageToken>'                       , 'staticPage.content'), //@TODO реализовать, пока для генерации урлов
);