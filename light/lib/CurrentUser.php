<?php
namespace light;
use Logger;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 12.05.12
 * Time: 14:41
 * To change this template use File | Settings | File Templates.
 */
require_once(ROOT_PATH.'system/exception/dataFormatException.php');
require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/cart/Cart.php');
require_once(ROOT_PATH.'lib/cart/SessionCartContainer.php');
require_once(ROOT_PATH.'lib/cart/V2cartPriceContainer.php');


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
   * @var Cart
   */
  private $cart;

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
   * @return Cart
   */
  public function getCart(){
    return $this->cart;
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

  /**
   * @param RegionData $region
   *
   */
  public function setRegion(RegionData $region){
    $this->setRegionByGeoIPCode($region->getGeoIpCode());
  }

  /**
   * @param string $geoIPCode
   * @throws dataFormatException
   */
  public function setRegionByGeoIPCode($geoIPCode){
    if(!App::getRegion()->isValidGeoIPCode($geoIPCode)){
      throw new dataFormatException('geoIPCode is not valid: '.$geoIPCode);
    }
    App::getResponse()->setCookie('geoshop', $geoIPCode);
  }

  /**
   * @param int $id
   */
  public function setRegionById($id){
    $code = App::getRegion()->getGeoIPCodeById($id);
    if(!is_null($code)){
      $this->setRegionByGeoIPCode($code);
    }
  }

  /**
   * Проверяет, установлен ли у пользователя регион (в противном случае getRegion вернет регион по геоip или дефолтный)
   * @return bool
   */
  public function isSelectedRegion(){
    return !empty($_COOKIE['geoshop']);
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
      $region = App::getRegion()->getByGeoIPCode($geoIpCode);
      if(!is_object($region)){
        throw new dataFormatException('not found region for geoIPCode: '.$geoIpCode);
      }
      $this->region = $region;
    }
    catch(dataFormatException $e){
      Logger::getRootLogger()->warn($e->getMessage());
      $this->region = App::getRegion()->getByGeoIPCode(self::DEFAULT_GEO_IP_CODE);
    }

    $this->cart = new Cart(new SessionCartContainer(), new MockCartPriceContainer());
  }
}
