<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 18.04.12
 * Time: 18:13
 * To change this template use File | Settings | File Templates.
 */

//require_once(ROOT_PATH.'model/DeliveryModel.php');
require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/helpers/DateFormatter.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');

class delivery
{

  /**
   * @param string $response
   * @param array $params
   */
  public function ProductDeliveryJson($response, $params=array()){
    TimeDebug::start('controller:delivery:ProductDeliveryJson');

    $result = App::getDelivery()->getProductDeliveries(intval($_POST['product_id']), intval($_POST['quantity']) , intval($_POST['region']));

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

  public function ProductListShortDeliveryJson($response, $params=array()){
    TimeDebug::start('controller:delivery:ProductDeliveryJson');

    if(!isset($_POST['products'])){
      $_POST['products'] = array();
    }
    if(!isset($_POST['region'])){
      $_POST['region'] = $region_id = 14974; //TODO реализовать класс CurrentUser
    }
    else{
      $_POST['region'] = (int) $_POST['region'];
    }

    $result = App::getDelivery()->getShortDeliveryInfoForProductList($_POST['products'], $_POST['region']);

    $return = array();
    foreach($result as $productId => $deliveries){
      $return[$productId] = array();
      foreach($deliveries as $delivery){
        $return[$productId][] = array(
          'typeId' => $delivery->getModeId(),
          'date' => DateFormatter::Humanize($delivery->getEarliestDate()),
          'token' => $delivery->getToken()
        );
      }
    }

    $response->setContentType('application/json');
    $response->setContent(json_encode(array("success" => true, 'data' => $return)));
    TimeDebug::end('controller:delivery:ProductDeliveryJson');
  }

}
