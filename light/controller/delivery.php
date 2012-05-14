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
   * @param Response $response
   * @param array $params
   */
  public function ProductDeliveryJson(Response $response, $params=array()){
    TimeDebug::start('controller:delivery:ProductDeliveryJson');
    $productId = isset($_POST['product_id'])? (int) $_POST['product_id'] : 0;
    $productQuantity = isset($_POST['product_quantity'])? (int) $_POST['product_quantity'] : 1;
    $regionId = isset($_POST['regionId'])? (int) $_POST['region_id'] : App::getCurrentUser()->getRegion()->getId();

    $result = App::getDelivery()->getProductDeliveries($productId, $productQuantity , $regionId);

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

  /**
   * @param Response $response
   * @param array $params
   */
  public function ProductListShortDeliveryJson(Response $response, $params=array()){
    TimeDebug::start('controller:delivery:ProductDeliveryJson');

    if(!isset($_POST['ids'])){
      $_POST['ids'] = array();
    }
    if(!isset($_POST['region'])){
      $_POST['region'] = $region_id = App::getCurrentUser()->getRegion()->getId();
    }
    else{
      $_POST['region'] = (int) $_POST['region'];
    }

    $result = App::getDelivery()->getShortDeliveryInfoForProductList($_POST['ids'], $_POST['region']);

    $return = array();
    foreach($result as $productId => $deliveries){
      /**
       * @var DeliveryShortData[] $deliveries
       */
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
