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
   * @var array key - geoIPCode, value - region id
   * @TODO перенести это на ядро
   */
  private $geoIPCodeMapping = array(
    '43'    => 99,
    '47_4'  => 1964,
    '47_3'  => 1965,
    '47_1'  => 6125,
    '47'    => 8440,
    '47_5'  => 9748,
    '47_2'  => 10358,
    '62'    => 10374,
    '09'    => 13241,
    '69'    => 13242,
    '77'    => 18073,
    '86'    => 18074,
    '48'    => 14974,
    '76'    => 74358,
    '41'    => 74562,
  );



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
    foreach($this->geoIPCodeMapping as $regCode => $regId){
      if($regId == $response[0]['id']){
        $region->setGeoIpCode((int) $regCode);
        break;
      }
    }
    return $region;
  }

  /**
   * @param string $code
   * @throws dataFormatException
   * @return RegionModel
   */
  public function getByGeoIPCode($code){
    if(!$this->isValidGeoIPCode($code)){
      throw new dataFormatException('invalid geoIp code: '.$code);
    }

    if(array_key_exists($code, $this->geoIPCodeMapping)){
      return $this->getById($this->geoIPCodeMapping[$code]);
    }

    return Null;
  }

  /**
   * @param int $id
   * @return string | null
   */
  public function getGeoIPCodeById($id){
    foreach($this->geoIPCodeMapping as $code => $codeId){
      if ($id == $codeId){
        return $code;
      }
    }

    return Null;
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
      foreach($this->geoIPCodeMapping as $regCode => $regId){
        if($regId == $geo['id']){
          $region->setGeoIpCode((int) $regCode);
          break;
        }
      }
      $regionList[] = $region;
    }

    return $regionList;
  }

  /**
   * @param string $code
   * @return bool
   */
  public function isValidGeoIPCode($code){
    return (bool) preg_match('/^[0-9a-zA-Z]+[-_0-9a-zA-Z]*$/i', $code);
  }

  public function Mock(){
    $region = new RegionData();
    $region->setId((int) 14974);
    $region->setName('Москва');
    $region->setToken('moskva');
    $region->setIsMain((bool) 1);
    $region->setGeoIpCode((int) 48);
    return $region;
  }
}
