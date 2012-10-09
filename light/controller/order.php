<?php
namespace light;
/**
* Created by JetBrains PhpStorm.
* User: Kuznetsov
* Date: 17.05.12
* Time: 12:47
* To change this template use File | Settings | File Templates.
*/
require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');

class orderController
{
  public function oneClick(Response $response, $params = array()){

//    exit();
    TimeDebug::start('controller:order:oneClick');

    $response->setContentType('application/json');

    if (App::getRequest()->getMethod() != Request::POST) {
      $response->setStatusCode(400);
      $response->setContent(json_encode(array('success' => false, 'message' => 'Не удалось создать заказ, ожидался Post-запрос')));
      TimeDebug::end('controller:order:oneClick');
      return;
    }

    try{
      $productToken = array_key_exists('product', $_GET)? $_GET['product'] : false;

      if(!$productToken){
        throw new \InvalidArgumentException('Не указан желаемый продукт');
      }

      if(!array_key_exists('order', $_POST)){
        throw new \InvalidArgumentException('Не удалось создать заказ, не переданы параметры заказа');
      }

      $productQuantity = (array_key_exists('product_quantity', $_POST['order']) && (intval($_POST['order']['product_quantity']) > 0))? (int)$_POST['order']['product_quantity'] : 1;

      $productList = App::getProduct()->getProductsByTokenList(array($productToken));
      if(!is_array($productList) || !array_key_exists(0, $productList)){
        throw new \RuntimeException('Не удалось создать заказ, передан неизвестный товар');
      }
      $product = $productList[0];
      $productList = array();
      if($product->isKit()){
        $kitList = $product->getKitList();
        foreach($kitList as $kitElem){
          $productList[] = array('id' => $kitElem->getProductId(), 'quantity' => ($kitElem->getQuantity() * $productQuantity));
        }
      }
      else{
        $productList = array(array('id' => $product->getId(), 'quantity' => $productQuantity));
      }

      $order = array(
        'delivered_at' => (array_key_exists('delivered_at', $_POST['order']) ? $_POST['order']['delivered_at']:''),
        'recipient_first_name'  => (array_key_exists('recipient_first_name', $_POST['order']) ? $_POST['order']['recipient_first_name']:''),
        'recipient_phonenumber' => (array_key_exists('recipient_phonenumber', $_POST['order']) ? $_POST['order']['recipient_phonenumber']:''),
        'product' => $productList
      );
      if(array_key_exists('shop_id', $_POST['order'])) $order['shop_id'] = (int) $_POST['order']['shop_id'];

      $order = App::getOrder()->getOrderFromOneClickArray($order);

      //@todo validator

      $order = App::getOrder()->save($order);


//      Send core requset post: http://api.enter.dev/index.php/v2/order/create?uid=5062eab6631eb&client_id=site
//Wed Sep 26 15:44:54 2012,626 [12919] INFO CoreClient - Request post:{"type_id":9,"geo_id":14974,"ip":"172.21.67.24, 10.0.3.254","payment_id":1,"delivery_type_id":3,"delivery_date":"2012-09-26","delivery_interval_id":null,"shop_id":1,"address_id":null,"address":null,"zip_code":null,"first_name":"vnxfghgf","last_name":null,"middle_name":null,"phone":"","mobile":null,"email":null,"extra":"Это быстрый заказ за 1 клик. Уточните параметры заказа у клиента.","is_receive_sms":false,"svyaznoy_club_card_number":null,"product":[{"quantity":1,"id":41206}],"service":[]}
//Wed Sep 26 15:44:56 2012,179 [12919] INFO CoreClient - Request time:1.5535409450531
//Wed Sep 26 15:44:56 2012,179 [12919] DEBUG CoreClient - Core response resource: Resource id #85
//Wed Sep 26 15:44:56 2012,180 [12919] DEBUG CoreClient - Core response info: {"content_type":"application\/json","http_code":200,"header_size":171,"request_size":936,"redirect_count":0,"total_time":1.552711,"namelookup_time":2.0e-5,"connect_time":0.000114,"pretransfer_time":0.000115,"size_upload":755,"size_download":206,"speed_download":132,"starttransfer_time":1.552687,"redirect_time":0,"certinfo":[],"redirect_url":""}
//Wed Sep 26 15:44:56 2012,180 [12919] DEBUG CoreClient - Core response: {"error":{"code":600,"message":"\u041d\u0435\u0438\u0437\u0432\u0435\u0441\u0442\u043d\u0430\u044f \u043e\u0448\u0438\u0431\u043a\u0430","1c":{"code":null,"message":"\u041e\u0448\u0438\u0431\u043a\u0430"}}}

      $jsonOrderData = json_encode(array (
        'order_article' => implode(',', array_map(function($i) { return $i->getId(); }, $order->getProductList())),
        'order_id' => $order->getNumber(),
        'order_total' => $order->getTotalPrice(),
        'product_quantity' => implode(',', array_map(function($i) { return $i->getQuantity(); }, $order->getProductList())),
      ));

      require_once(Config::get('helperPath').'Counters.php');
      Counters::setParam('jsonOrderData', $jsonOrderData);
      Counters::setParam('orderData', $order);

      try{
        if($order->getDeliveryInfo()->getShopId() > 0){
          $shopInfo = App::getShop()->getById($order->getDeliveryInfo()->getShopId());
          $shopName = $shopInfo->getName();

          $shopData = array('name' => $shopName, 'region' => $shopInfo->getRegionId(), 'regime' => $shopInfo->getRegtime(), 'address' => $shopInfo->getAddress());
        }
        else{
          $shopData = false;
          $shopName = '';
        }
      }
      catch(\Exception $e){
        $shopData = false;
        $shopName ='';
      }

      $content = App::getHtmlRenderer()->renderFile(
        'order/orderOneClickSuccess',
        array(
          'order' => $order,
          'product' => $product,
          'productQuantity' => $productQuantity,
          'region' =>App::getCurrentUser()->getRegion(),
          'shopName' => $shopName
        )
      );

      $return['success'] = true;
      $return['message'] = 'Заказ успешно создан';
      $return['data'] = array(
        'title' => 'Ваш заказ принят, спасибо!',
        'content' => $content,
        'shop' => $shopData,
      );
    }
    catch(\InvalidArgumentException $e){
      $response->setStatusCode(400);
      $return['success'] = false;
      $return['message'] = 'Не удалось создать заказ' . (sfConfig::get('sf_debug') ? (' Ошибка: ' . $e->getMessage()) : '');
    }
    catch(\LogicException $e){
      $response->setStatusCode(500);
      $return['success'] = false;
      $return['message'] = 'Не удалось создать заказ' . (sfConfig::get('sf_debug') ? (' Ошибка: ' . $e->getMessage()) : '');
    }
    TimeDebug::end('controller:order:oneClick');
    $response->setContent(json_encode($return));
  }
}
