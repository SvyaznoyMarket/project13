<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 13.04.12
 * Time: 14:49
 * To change this template use File | Settings | File Templates.
 */

require_once(ROOT_PATH.'system/exception/dataFormatException.php');
require_once(ROOT_PATH.'lib/CoreClient.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
require_once(HELPER_PATH.'DateFormatter.php');
require_once(VIEW_PATH.'dataObject/DeliveryData.php');

class DeliveryModel
{

  /**
   * @param integer $productId
   * @param integer $productQuantity
   * @param integer $geoId
   * @throws dataFormatException
   * @return DeliveryData[]
   */
  public function getProductDeliveries($productId, $productQuantity, $geoId){
    if(!is_int($productId)){
      throw new dataFormatException('$productId must be int, but in real is ('.gettype($productId).') '.$productId);
    }
    if(!is_int($productQuantity)){
      throw new dataFormatException('$productQuantity must be int, but in real is ('.gettype($productQuantity).') '.$productQuantity);
    }
    if(!is_int($geoId)){
      throw new dataFormatException('$geoId must be int, but in real is ('.gettype($geoId).') '.$geoId);
    }
    TimeDebug::start('DeliveryModel:getProductDeliveries:clientV1');
    $data = CoreV1Client::getInstance()->query('order.calc', array(), array(
      'geo_id'  => $geoId,
      'product' => array(array('id' => $productId, 'quantity' => $productQuantity)),
      'service' => null,
      'mode'    => null
    ));
//    var_export($data);
    TimeDebug::end('DeliveryModel:getProductDeliveries:clientV1');

    $return = array();

    TimeDebug::start('DeliveryModel:getProductDeliveries:dataConvert');

    foreach($data['deliveries'] as $deliveryTypeName => $delivery){
        if(!isset($data['products'][$productId]['deliveries'][$deliveryTypeName])){
          echo "{$deliveryTypeName} not found \r\n";
          continue;
        }
        switch($delivery['token']){
          case 'self':
            $return = $this->addShopToSelfDelivery($data['products'][$productId]['deliveries'][$deliveryTypeName]['dates'], $delivery, $data['shops'][$delivery['shop_id']], $return);
            break;
          default:
            $deliveryData = new DeliveryData();
            $deliveryData->setModeId($delivery['mode_id']);
            $deliveryData->setName(($delivery['token'] =='standart')? 'курьерская доставка' : $delivery['name']);
            $deliveryData->setToken($delivery['token']);
            $deliveryData->setPrice($data['products'][$productId]['deliveries'][$deliveryTypeName]['price']);

            $deliveryDates = array();
            foreach($data['products'][$productId]['deliveries'][$deliveryTypeName]['dates'] as $date){
              $date['date'] = substr($date['date'], 0, 10);
              $deliveryDates[] = array(
                'name' => DateFormatter::CoreFullDateToOrderForm($date['date']),
                'value' => $date['date']
              );
            }

            $deliveryData->setDates($deliveryDates);
            $return[] = $deliveryData;
        }
    }
    TimeDebug::end('DeliveryModel:getProductDeliveries:dataConvert');
    return $return;

  }

  /**
   * @param array $dates
   * @param array $delivery
   * @param array $ShopInfo
   * @param DeliveryData[] $return
   */
  protected function addShopToSelfDelivery($dates, $delivery, $ShopInfo, $return){
    TimeDebug::start('DeliveryModel:getProductDeliveries:addShop');
    $returnDeliv = Null;
    $returnKey = Null;
    foreach($return as $key => $deliv){
      if($deliv->getToken() == 'self'){
        $returnKey = $key;
        $returnDeliv = $deliv;
        break;
      }
    }
    if(is_null($returnKey)){
      $returnDeliv = new DeliveryData();
      $returnDeliv->setModeId($delivery['mode_id']);
      $returnDeliv->setName($delivery['orig_name']);
      $returnDeliv->setToken($delivery['token']);
      $returnDeliv->setPrice(0);
    }
    $shopData = new ShopData();
    $shopData->setId($ShopInfo['id']);
    $shopData->setAddress($ShopInfo['address']);
    $shopData->setRegtime($ShopInfo['working_time']);
    $shopData->setLatitude($ShopInfo['coord_lat']);
    $shopData->setLongitude($ShopInfo['coord_long']);
    $returnDeliv->addShop($shopData);

    $deliveryDates = $returnDeliv->getDates();

    foreach($dates as $date){ //просматриваем даты полученной доставки
      $date['date'] = substr($date['date'], 0, 10);
      $finded = false;
      foreach($deliveryDates as $key => $deliveryDate){ // просматриваем уже заполненные даты доставки
        if($deliveryDate['value'] == $date['date']){ //У нас уже есть дата доставки, нужно лишь добавить инфу о магазине
          $deliveryDates[$key]['shops'][$ShopInfo['id']] = array();
          foreach($date['interval'] as $interval){
            $deliveryDates[$key]['shops'][$ShopInfo['id']][$interval['id']] = array(
              'time_begin'  => $interval['time_begin'],
              'time_end'    => $interval['time_end']
            );
          }
          $finded = true;
        }
      }
      if(!$finded){ //На эту дату доставок еще не было - нужно создавать
        $deliveryInfo = array(
          'name' => DateFormatter::CoreFullDateToOrderForm($date['date']),
          'value' => $date['date'],
          'shops' => array($ShopInfo['id'] => array())
        );
        foreach($date['interval'] as $interval){
          $deliveryInfo['shops'][$ShopInfo['id']][$interval['id']] = array(
            'time_begin'  => $interval['time_begin'],
            'time_end'    => $interval['time_end']
          );
        }
        $deliveryDates[] = $deliveryInfo;
      }
    }
    $returnDeliv->setDates($deliveryDates);

    if(is_null($returnKey)){
      $return[] = $returnDeliv;
    }
    else{
      $return[$returnKey] = $returnDeliv;
    }
    TimeDebug::end('DeliveryModel:getProductDeliveries:addShop');
    return $return;
  }
}
