<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 12.05.12
 * Time: 14:41
 * To change this template use File | Settings | File Templates.
 */
require_once(ROOT_PATH.'system/App.php');

class CurrentUser
{
  const DEFAULT_GEO_IP_CODE = 48;

  /**
   * @var RegionData
   */
  private $region;

  /**
   * @var string
   */
  private $ip;

  /**
   * @static
   * @return CurrentUser
   */
  public static function getInstance()
  {
    static $instance;
    if (!$instance) {
      $instance = new CurrentUser();
    }
    return $instance;
  }

  /**
   * @return RegionData
   */
  public function getRegion(){
    return $this->region;
  }

  /**
   * @return string
   */
  public function getIp(){
    return $this->ip;
  }

  private function __construct()
  {
    if (!empty($_SERVER['X-Real-IP'])) {
      $this->ip = $_SERVER['X-Real-IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $this->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif (!empty($_SERVER['REMOTE_ADDR'])) {
      $this->ip = $_SERVER['REMOTE_ADDR'];
    }
    else {
      $this->ip = null;
    }

    if (!empty($_COOKIE['geoshop'])) {
      $geoIpCode = $_COOKIE['geoshop'];
    }
    elseif(!empty($_SERVER['HTTP_X_GEOIP_REGION'])){
      $geoIpCode = $_SERVER['HTTP_X_GEOIP_REGION'];
    }
    else{
      $geoIpCode = self::DEFAULT_GEO_IP_CODE;
    }

    try{
      $this->region = App::getRegion()->getByGeoipCode($geoIpCode);
    }
    catch(dataFormatException $e){
      Logger::getRootLogger()->warn($e->getMessage());
      $this->region = App::getRegion()->getByGeoipCode(self::DEFAULT_GEO_IP_CODE);
    }
  }
}
