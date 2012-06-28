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
require_once(ROOT_PATH.'lib/cart/V2CartPriceContainer.php');

require_once(VIEW_PATH.'dataObject/UserData.php');


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

  /** @var UserData */
  private $user;

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

  public function isAuthorized(){
    return is_null($this->getUser());
  }

  /**
   * @return UserData|null
   */
  public function getUser(){
    if(is_object($this->user)){
      return $this->user;
    }
    try{
      $user_id = isset($_SESSION['symfony/user/sfUser/attributes']['guard']['user_id']) ? $_SESSION['symfony/user/sfUser/attributes']['guard']['user_id'] : null;
      $user_id = (int)$user_id;
      if ($user_id < 1) {
        return null;
      }

      if (!($conn = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD))) {
        throw new \Exception('cant connect to db "' . DB_HOST . '"');
      }
      if (!mysql_select_db(DB_NAME, $conn)) {
        throw new \Exception('cant select db "' . DB_NAME . '"');
      }

      $query = 'SELECT first_name,last_name, middle_name  FROM guard_user WHERE id="' . $user_id . '"';


      if ($result = mysql_query($query, $conn)) {
        $userData = mysql_fetch_array($result, MYSQL_ASSOC);
        if (!$userData) {
          throw new \Exception('mysql_fetch_array error');
        }

        $this->user = new UserData($userData);
        mysql_close($conn);
        return $this->user;
      }
      else{
        throw new \Exception('mysql_query error');
      }
    }
    catch(\Exception $e){
      if ($conn) {
        mysql_close($conn);
      }
      return null;
    }
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
