<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 18.04.12
 * Time: 18:13
 * To change this template use File | Settings | File Templates.
 */

require_once(ROOT_PATH.'model/DeliveryModel.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');

class delivery
{

  /**
   * @param Array $params
   * @param Response $response
   */
  public function ProductDeliveryJson($params, Response $response){
    TimeDebug::start('controller:delivery:ProductDeliveryJson');

    $return = array();
    $model = new DeliveryModel();
    $result = $model->getProductDeliveries(intval($params['product_id']), intval($params['quantity']) ,$params['region']);

    $return = array('success' => true, 'data' => array());
    foreach($result as $deliveryObject){
      $deliveryArray = $deliveryObject->toArray();
      foreach($deliveryArray['dates'] as $key=>$date){
        if($key > 7){
          unset($deliveryArray['dates'][$key]);
          continue;
        }
        if(!isset($date['shops'])){
          continue;
        }
        $shops = $date['shops'];
        unset($deliveryArray['dates'][$key]['shops']);
        if(count($shops > 0)){
          $deliveryArray['dates'][$key]['shopIds'] = array_keys($shops);
        }
      }
      $return['data'][$deliveryArray['token']] = $deliveryArray;
    }

    $return['currentDate'] = date('Y-m-d');

    $response->setContentType('application/json');
    $response->setContent(json_encode($return));
    TimeDebug::end('controller:delivery:ProductDeliveryJson');
  }

  public function ProductListShortDeliveryJson($params, Response $response){
    TimeDebug::start('controller:delivery:ProductDeliveryJson');
    $return = array();

    if(!isset($params['products'])){
      $params['products'] = Null;
    }
    if(!isset($params['region'])){
      $params['region'] = Null;
    }

    $model = new DeliveryModel();
    $result = $model->getShortDeliveryInfoForProductList($params['products'], $params['region']);

    $return = $result;


    $response->setContentType('application/json');
    $response->setContent(json_encode($return));
    TimeDebug::end('controller:delivery:ProductDeliveryJson');
  }

}
