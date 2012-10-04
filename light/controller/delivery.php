<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 18.04.12
 * Time: 18:13
 * To change this template use File | Settings | File Templates.
 */

//require_once(Config::get('rootPath').'model/DeliveryModel.php');
require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/helpers/DateFormatter.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');

class deliveryController
{

  /**
   * @param Response $response
   * @param array $params
   */
  public function ProductDeliveryJson(Response $response, $params=array()){
    TimeDebug::start('controller:delivery:ProductDeliveryJson');
    $productId = isset($_POST['product_id'])? (int) $_POST['product_id'] : 0;
    $productQuantity = isset($_POST['product_quantity'])? (int) $_POST['product_quantity'] : 1;
    $regionId = (isset($_POST['regionId']) && (intval($_POST['regionId']) > 0))? (int) $_POST['region_id'] : App::getCurrentUser()->getRegion()->getId();

    try
    {
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
    }
    catch (\Exception $e)
    {
      $return = array('success' => false, 'data' => array());
    }

    $return['currentDate'] = date('Y-m-d');

    $response->setContentType('application/json');
    $response->setContent(json_encode($return));

    #$renderer = App::getHtmlRenderer();
    #$renderer->setPage('empty');
    #$response->setContent($renderer->render());

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

    try
    {
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
            'token' => $delivery->getToken(),
            'price' => $delivery->getPrice()
          );
        }
      }
      $response->setContent(json_encode(array("success" => true, 'data' => $return)));
    }
    catch (\Exception $e)
    {
      $response->setContent(json_encode(array("success" => false, 'data' => array())));
    }

    $response->setContentType('application/json');
    TimeDebug::end('controller:delivery:ProductDeliveryJson');
  }

}
