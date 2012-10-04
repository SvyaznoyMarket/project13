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
  array('/user/shortinfo'                           , 'user.getShortInfo'),
  array('/product/delivery1click'                   , 'delivery.ProductDeliveryJson'),
  array('/product/delivery-info/<productId>'        , 'delivery.ProductListShortDeliveryJson'),
  array('/product/delivery-info'                    , 'delivery.ProductListShortDeliveryJson'),
  array('/cart/clear'                                  , 'cart.clear'),
  array('/cart/add/<productId>/_quantity/<quantity>'   , 'cart.setProductQuantity'),
  array('/cart/add/<productId>/_quantity/'             , 'cart.setProductQuantity'),
  array('/cart/delete/<productId>/_service/<serviceId>', 'cart.deleteProduct'),
  array('/cart/delete/<productId>/_service/'           , 'cart.deleteProduct'),
  array('/cart/add_service/<productId>/_service/<serviceId>/_quantity/<quantity>', 'cart.addService'),
  array('/cart/add_service/<productId>/_service/<serviceId>/_quantity/'          , 'cart.addService'),
  array('/cart/add_service/_service/<serviceId>/_quantity/<quantity>'            , 'cart.addService'),
  array('/cart/add_service/_service/<serviceId>/_quantity/'                      , 'cart.addService'),
  array('/cart/delete_service/<productId>/_service/<serviceId>'                  , 'cart.deleteService'),
  array('/cart/delete_service/_service/<serviceId>'                              , 'cart.deleteService'),
  array('/category/main_menu'                       , 'catalog.MainMenu'),
  array('/region/init'                              , 'region.getShopAvailable'),

  array('/product-view/<productId>'          , 'smartengine.view'),
  array('/product-buy/<product>'             , 'smartengine.buy'),
  array('/orders/new'                        , 'order.new'), //@TODO реализовать, пока для генерации урлов
  array('/product/<productToken>'            , 'product.show'), //@TODO реализовать, пока для генерации урлов
  array('/catalog/<categoryToken>/'          , 'catalog.showCategory'), //@TODO реализовать, пока для генерации урлов
  array('/products/set/<productBarcodeList>' , 'product.set'), //@TODO реализовать, пока для генерации урлов
  array('/search'                            , 'search.form'), //@TODO реализовать, пока для генерации урлов
  array('/shops/<regionToken>'               , 'shop.regionList'), //@TODO реализовать, пока для генерации урлов
  array('/private/'                          , 'user.index'), //@TODO реализовать, пока для генерации урлов

  array('/region/change/<region>'            , 'region.change'), //@TODO реализовать, пока для генерации урлов
  array('/cart/'                             , 'cart.index'), //@TODO реализовать, пока для генерации урлов
  array('/login/<provider>'                  , 'user.signin'), //@TODO реализовать, пока для генерации урлов
  array('/logout'                            , 'user.logout'), //@TODO реализовать, пока для генерации урлов
  array('/region/autocomplete'               , 'region.autocomplete'), //@TODO реализовать, пока для генерации урлов
  array('/region/change/<region>'            , 'region.change'), //@TODO реализовать, пока для генерации урлов

  array('/f1/show/<service>'                 , 'service.show'), //@TODO реализовать, пока для генерации урлов
  array('/f1/<category>'                     , 'service.category'), //@TODO реализовать, пока для генерации урлов
  array('/f1'                                , 'service.index'), //@TODO реализовать, пока для генерации урлов


  array('/'                                  , 'staticPage.mainPage'),
  array('/<pageToken>'                       , 'staticPage.content'), //@TODO реализовать, пока для генерации урлов
);