<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 31.07.12
 * Time: 13:18
 * To change this template use File | Settings | File Templates.
 */

require_once(Config::get('viewPath').'dataObject/order/OrderData.php');
require_once(Config::get('viewPath').'dataObject/DeliveryData.php');

class OrderModel
{

  public function getOrderFromArray($data){
    if(!is_array($data)){
      throw new \InvalidArgumentException('Недопустимые данные для создания заказа');
    }

    $user = App::getCurrentUser()->getUser();

    if($user){
      $data['user_id'] = $user->getId();
      if(!array_key_exists('last_name', $data)) $data['last_name'] = $user->getLastName();
      if(!array_key_exists('first_name', $data)) $data['first_name'] = $user->getFirstName();
      if(!array_key_exists('middle_name', $data)) $data['middle_name'] = $user->getMiddleName();
      if(!array_key_exists('email', $data)) $data['email'] = $user->getEmail();
    }

    $order = new OrderData($data);

    $order->setGeoId(App::getCurrentUser()->getRegion()->getId());
    $order->setTypeId(OrderData::TYPE_ORDER);
    $order->setIp(App::getCurrentUser()->getIp());

    return $order;
  }

  /**
   * @param array $data
   * @param DeliveryData[] $possibleDeliveries
   * @throws \InvalidArgumentException
   * @return OrderData
   */
  public function getOrderFromOneClickArray($data){
    if(!is_array($data)){
      throw new \InvalidArgumentException('Недопустимые данные для создания заказа');
    }

    $orderData = array();

    if(array_key_exists('shop_id', $data)){
      $orderData['shop_id'] = (int) $data['shop_id'];
      $orderData['delivery_type_id'] = DeliveryData::TYPE_SELF;
    }
    else{
      $orderData['delivery_type_id'] = DeliveryData::TYPE_STANDART;
    }
    if(array_key_exists('delivered_at', $data)) $orderData['delivery_date'] = (string) $data['delivered_at'];
    if(array_key_exists('recipient_first_name', $data)) $orderData['first_name'] = (string) $data['recipient_first_name'];
    if(array_key_exists('recipient_phonenumber', $data)) $orderData['mobile'] = (string) $data['recipient_phonenumber'];
    if(array_key_exists('product', $data)) $orderData['product'] = $data['product'];
    if(array_key_exists('svyaznoy_club_card_number', $data)) $orderData['svyaznoy_club_card_number'] = $data['svyaznoy_club_card_number'];

    $order = $this->getOrderFromArray($orderData);
    $order->setTypeId(OrderData::TYPE_1CLICK);
    $order->setPaymentId(1); //Оплата наличными
    $order->setExtra('Это быстрый заказ за 1 клик. Уточните параметры заказа у клиента.');
    return $order;
  }

  public function getStatusList(){
    return array(
      array('id' => OrderData::STATUS_FORMED,                  'token' => 'created',   'name' => 'Новый заказ'),
      array('id' => OrderData::STATUS_APPROVED_BY_CALL_CENTER, 'token' => 'confirmed', 'name' => 'Подтвержден'),
      array('id' => OrderData::STATUS_FORMED_IN_STOCK,         'token' => 'assembled', 'name' => 'Собран на складе'),
      array('id' => OrderData::STATUS_IN_DELIVERY,             'token' => 'delivery',  'name' => 'Доставляется'),
      array('id' => OrderData::STATUS_DELIVERED,               'token' => 'received',  'name' => 'Выполнен'),
      array('id' => OrderData::STATUS_CANCELED,                'token' => 'cancelled', 'name' => 'Отменен'),
    );
  }

  /**
   * @param OrderData $order
   * @return OrderData
   * @throws \LogicException
   */
  public function save(OrderData $order){
    $get = array();
    if(App::getCurrentUser()->isAuthorized()) $get = array("token" => App::getCurrentUser()->getAuthToken());

    $post = array(
      "type_id"          => $order->getTypeId(),
      "geo_id"           => App::getCurrentUser()->getRegion()->getId(),
      "ip"               => $order->getIp(),
      "payment_id"       => $order->getPaymentId(),
      "delivery_type_id" => $order->getDeliveryInfo()->getDeliveryTypeId(),
      "delivery_date"    => $order->getDeliveryInfo()->getDeliveryDate(),
      "delivery_interval_id" => $order->getDeliveryInfo()->getDeliveryIntervalId(),
      "shop_id"          => $order->getDeliveryInfo()->getShopId(),
      "address_id"       => $order->getDeliveryInfo()->getAddressId(),
      "address"          => $order->getDeliveryInfo()->getAddress(),
      "zip_code"         => $order->getDeliveryInfo()->getZipCode(),
      "first_name"       => $order->getUser()->getFirstName(),
      "last_name"        => $order->getUser()->getLastName(),
      "middle_name"      => $order->getUser()->getMiddleName(),
      "phone"            => $order->getUser()->getPhone(),
      "mobile"           => $order->getUser()->getMobile(),
      "email"            => $order->getUser()->getEmail(),
      "extra"            => $order->getExtra(),
      "is_receive_sms"   => $order->isReceiveSms(),
      "svyaznoy_club_card_number" => $order->getSvyaznoyClubCardNumber(),
//      "product"          => array(),
//      "service"          => array(),
    );



    foreach($order->getProductList() as $product){
      if(!array_key_exists('product', $post)){
        $post['product'] = array();
      }
      $post['product'][] = array("quantity" => $product->getQuantity(),"id" => $product->getId());
    }
    foreach($order->getServiceList() as $service){
      if(!array_key_exists('service', $post)){
        $post['service'] = array();
      }
      $post['service'][] = array("quantity" => $service->getQuantity(),"id" => $service->getId(), "product_id" =>$service->getProductId());
    }

    $result = App::getCoreV2()->query('order.create', $get, $post);

    if(is_array($result) && array_key_exists('confirmed', $result) && $result['confirmed'] == 'true'){
      $order->setId((int) $result['id']);
      $order->setNumber((string) $result['number']);

      if (array_key_exists('price', $result)) $order->setTotalPrice($result['price']);
    }
    else{
      throw new \LogicException($result['message'], $result['code']);
    }

    return $order;

  }

}
