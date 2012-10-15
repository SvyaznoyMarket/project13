<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 13.04.12
 * Time: 14:49
 * To change this template use File | Settings | File Templates.
 */

require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');
require_once(Config::get('helperPath').'DateFormatter.php');
require_once(Config::get('viewPath').'dataObject/DeliveryData.php');
require_once(Config::get('viewPath').'dataObject/DeliveryShortData.php');

class DeliveryModel
{

  /**
   * @param array $productIds
   * @param int $geoId
   * @throws InvalidArgumentException
   * @return array
   * <code>
   *  array(
   *    productId => array(
   *      DeliveryShortData,
   *      DeliveryShortData,
   *      DeliveryShortData
   *    ),
   *    productId => array(
   *      DeliveryShortData,
   *      DeliveryShortData,
   *      DeliveryShortData
   *    )
   *  )
   * </code>
   *
   */
  public function getShortDeliveryInfoForProductList($productIds, $geoId){
    if(!is_array($productIds)){
      throw new \InvalidArgumentException('$productIds must be array, but in real is ('.gettype($productIds).') '.print_r($productIds, true));
    }
    if(!is_int($geoId)){
      throw new \InvalidArgumentException('$geoId must be int, but in real is ('.gettype($geoId).') '.print_r($geoId, true));
    }
    $geoId = (int) $geoId;

    $params = array('product_list' => array());
    $productIds = array_unique($productIds);
    foreach($productIds as $productId){
      $params['product_list'][] = array('id' => (int) $productId, 'quantity' => 1);
    }

//    $params = array('product' => array(array('id' => 4435, 'quantity' =>1)));

    TimeDebug::start('DeliveryModel:getShortDeliveryInfoForProductList:clientV2');
    $data = App::getCoreV2()->query('delivery.calc', array('geo_id' => $geoId), $params);
    TimeDebug::end('DeliveryModel:getShortDeliveryInfoForProductList:clientV2');

    if(empty($data['product_list'])){
      throw new \UnexpectedValueException('Core result has no needed data: '.print_r($data, 1));
    }

    $return = array();

    foreach($productIds as $productId){
      $productId = (int) $productId;
      $return[$productId] = array();

      if(!isset($data['product_list'][$productId]) || !isset($data['product_list'][$productId]['delivery_mode_list'])){
        continue;
      }
      foreach($data['product_list'][$productId]['delivery_mode_list'] as $delivery){
        $deliveryShortObject = new DeliveryShortData();
        $deliveryShortObject->setId($delivery['delivery_id']);
        $deliveryShortObject->setModeId($delivery['id']);
        $deliveryShortObject->setPrice($delivery['price']);
        $deliveryShortObject->setEarliestDate($delivery['date_list'][0]['date']);
        $deliveryShortObject->setName($delivery['name']);
        $deliveryShortObject->setToken($delivery['token']);
        $return[$productId][] = $deliveryShortObject;
      }
    }

    return $return;
  }

  /**
   * @param integer $productId
   * @param integer $productQuantity
   * @param integer $geoId
   * @throws InvalidArgumentException
   * @return DeliveryData[]
   */
  public function getProductDeliveries($productId, $productQuantity, $geoId){
    if(!is_int($productId)){
      throw new \InvalidArgumentException('$productId must be int, but in real is ('.gettype($productId).') '.print_r($productId, true));
    }
    if(!is_int($productQuantity)){
      throw new \InvalidArgumentException('$productQuantity must be int, but in real is ('.gettype($productQuantity).') '.print_r($productQuantity, true));
    }
    if(!is_int($geoId)){
      throw new \InvalidArgumentException('$geoId must be int, but in real is ('.gettype($geoId).') '.print_r($geoId, true));
    }
    TimeDebug::start('DeliveryModel:getProductDeliveries:clientV1');

    $dataProduct = array(
        'product_list' => array(
            array(
                'id'       => $productId,
                'quantity' => $productQuantity
            )
        )
    );

    $data = App::getCoreV2()->query('delivery.calc', array('geo_id'  => $geoId), $dataProduct);

    TimeDebug::end('DeliveryModel:getProductDeliveries:clientV1');

    $return = array();

    TimeDebug::start('DeliveryModel:getProductDeliveries:dataConvert');

    $arr = isset($data['product_list'])?$data['product_list']:array();
    $productData = array_pop($arr);
    $productDeliveryListData = $productData['delivery_mode_list'];
    foreach((array)$productDeliveryListData as $productDeliveryData)
    {
        $shopDataList = array();

        $deliveryToken = $productDeliveryData['token'];
        $deliveryData = new DeliveryData();
        $deliveryData->setModeId($productDeliveryData['id']);
        $deliveryData->setName(($deliveryToken =='standart')? 'курьерская доставка' : $productDeliveryData['name']);
        $deliveryData->setToken($deliveryToken);
        $deliveryData->setPrice((int)$productDeliveryData['price']);

        $productDeliveryDateList = array();
        foreach($productDeliveryData['date_list'] as $dateData)
        {
            $productDeliveryDateData = array(
                'name' => DateFormatter::Humanize($dateData['date']),
                'value' => $dateData['date']
            );

            if($deliveryToken == 'self')
            {
                $shopList = array();
                foreach($dateData['shop_list'] as $shopLink)
                {
                    if(!isset($shopDataList[$shopLink['id']]))
                    {
                        $shopDataList[$shopLink['id']] = $this->getShopData($shopLink['id'], $data['shop_list']);
                    }

                    $shopList[$shopLink['id']] = $this->getShopIntervalList($shopLink['interval_list'], $data['interval_list']);
                }

                $productDeliveryDateData['shops'] = $shopList;
            }
            else{
              $productDeliveryDateData['intervals'] = array_key_exists('interval_list', $dateData)?$dateData['interval_list']: array();
            }

            $productDeliveryDateList[] = $productDeliveryDateData;
        }

        $deliveryData->setDates($productDeliveryDateList);

        if($deliveryToken == 'self')
        {
            foreach($shopDataList as $shopData)
            {
                $shopDataObject = new ShopData();
                $shopDataObject->setId($shopData['id']);
                $shopDataObject->setName($shopData['name']);
                $shopDataObject->setAddress($shopData['address']);
                $shopDataObject->setRegtime($shopData['working_time']);
                $shopDataObject->setLatitude($shopData['coord_lat']);
                $shopDataObject->setLongitude($shopData['coord_long']);
                $deliveryData->addShop($shopDataObject);
            }
        }

        $return[] = $deliveryData;
    }
    TimeDebug::end('DeliveryModel:getProductDeliveries:dataConvert');

    return $return;

  }

    private function getShopData($shopId, $shopList)
    {
        $shopData = Null;
        foreach($shopList as $shop)
        {
            if($shop['id'] == $shopId)
            {
                $shopData = $shop;

                break;
            }
        }

        return $shopData;
    }

    private function getShopIntervalList($intervalIdList, $intervalList)
    {
        $intervalDataList = array();
        foreach($intervalList as $interval)
        {
            if(in_array($interval['id'], $intervalIdList))
            {
                unset($interval['id']);
                $intervalDataList[] = $interval;
            }
        }

        return $intervalDataList;
    }
}
