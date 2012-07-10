<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 12.05.12
 * Time: 16:31
 * To change this template use File | Settings | File Templates.
 */

require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
require_once(VIEW_PATH.'dataObject/RegionData.php');
require_once(ROOT_PATH.'system/exception/systemException.php');
require_once(ROOT_PATH.'system/exception/dataFormatException.php');

class RegionModel
{
  /**
   * @param int $id
   * @return RegionModel
   */
  public function getById($id){
    $id = (int) $id;
    $response = App::getCoreV2()->query('geo.get-by-id-list', array(), array('id_list' => array($id)));
    if (!isset($response[0])){
      return null;
    }
    $region = new RegionData();
    $region->setId((int) $response[0]['id']);
    $region->setName($response[0]['name']);
    $region->setToken($response[0]['token']);
    $region->setIsMain((bool) $response[0]['is_main']);

    return $region;
  }

  /**
   * @param string $code
   * @return bool
   */
  public function isValidId($id){
    return (bool) preg_match('/^[0-9a-zA-Z]+[-_0-9a-zA-Z]*$/i', $id);
  }

  /**
   * Функция возвращает список активных городов, в которых естть наши магазины
   * @return RegionData[]
   * @throws systemException
   */
  public function getShopAvailable(){
    $response = App::getCoreV2()->query('geo.get-shop-available', array(), array());
    if (!isset($response[0])){
      return array();
    }

    $regionList = array();

    if(!is_array($response)){
      throw new systemException('null response from core');
    }
    foreach($response as $geo){
      $region = new RegionData();
      $region->setId((int) $geo['id']);
      $region->setName($geo['name']);
      $region->setToken($geo['token']);
      $region->setIsMain((bool) $geo['is_main']);
      $regionList[] = $region;
    }

    return $regionList;
  }

  public function Mock(){
    $region = new RegionData();
    $region->setId((int) 14974);
    $region->setName('Москва');
    $region->setToken('moskva');
    $region->setIsMain((bool) 1);
    return $region;
  }
}
