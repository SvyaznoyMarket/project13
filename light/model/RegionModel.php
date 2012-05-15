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
  public function getByGeoipCode($code){
    if(!preg_match('/^[0-9a-zA-Z]+[-_0-9a-zA-Z]*$/i', $code)){
      throw new dataFormatException('invalid geoIp code: '.$code);
    }

    if(array_key_exists($code, $this->geoIPCodeMapping)){
      return $this->getById($this->geoIPCodeMapping[$code]);
    }

    return Null;
  }
}