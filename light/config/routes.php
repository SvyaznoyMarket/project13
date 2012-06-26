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
  array('/product/delivery1click'                   , 'delivery.ProductDeliveryJson'),
  array('/product/delivery-info/<productId>'        , 'delivery.ProductListShortDeliveryJson'),
  array('/product/delivery-info'                    , 'delivery.ProductListShortDeliveryJson'),
  array('/cart/clear'                                  , 'cart.clear'),
  array('/cart/add/<productId>/_quantity/<quantity>'   , 'cart.addProduct'),
  array('/cart/add/<productId>/_quantity/'             , 'cart.addProduct'),
  array('/cart/delete/<productId>/_service/<serviceId>', 'cart.deleteProduct'),
  array('/cart/delete/<productId>/_service/'           , 'cart.deleteProduct'),
  array('/cart/add_service/<productId>/_service/<serviceId>/_quantity/<quantity>', 'cart.addService'),
  array('/cart/add_service/<productId>/_service/<serviceId>/_quantity/'          , 'cart.addService'),
  array('/cart/add_service/_service/<serviceId>/_quantity/<quantity>'            , 'cart.addService'),
  array('/cart/add_service/_service/<serviceId>/_quantity/'                      , 'cart.addService'),
  array('/category/main_menu'                       , 'catalog.MainMenu'),
  array('/region/init'                              , 'region.getShopAvailable'),
  array('/'                                         , 'staticPage.mainPage'),
  array('/orders/new'                        , 'order.new'), //@TODO реализовать, пока для генерации урлов
  array('/product/<productToken>'            , 'product.show'), //@TODO реализовать, пока для генерации урлов
  array('/catalog/<categoryToken>/'          , 'catalog.showCategory'), //@TODO реализовать, пока для генерации урлов
  array('/products/set/<productBarcodeList>' , 'product.set'), //@TODO реализовать, пока для генерации урлов
  array('/search'                            , 'search.form'), //@TODO реализовать, пока для генерации урлов
  array('/shops/<regionToken>'               , 'shop.regionList'), //@TODO реализовать, пока для генерации урлов
  array('/private/'                          , 'user.index'), //@TODO реализовать, пока для генерации урлов
  array('/<pageToken>'                       , 'staticPage.content'), //@TODO реализовать, пока для генерации урлов
);