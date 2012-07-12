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
require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/cart/Cart.php');
require_once(ROOT_PATH.'lib/cart/SessionCartContainer.php');
require_once(ROOT_PATH.'lib/cart/V2CartPriceContainer.php');

require_once(VIEW_PATH.'dataObject/UserData.php');


class CurrentUser
{
  const DEFAULT_REGION_ID = 14974;
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
    return (bool)$this->getUser();
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
    $this->setRegionById($region->getId());
  }

  /**
   * @param string $geoIPCode
   * @throws UnexpectedValueException
   */
  public function setRegionById($id){
    if(!App::getRegion()->isValidId($id)){
      throw new \InvalidArgumentException('region id is not valid: '.$id);
    }
    App::getResponse()->setCookie('geoshop', $id);
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

    $regionId = !empty($_COOKIE['geoshop'])?$_COOKIE['geoshop']:self::DEFAULT_REGION_ID;

    try{
      $region = App::getRegion()->getById($regionId);
      if(!is_object($region)){
        throw new \UnexpectedValueException('not found region for id: ' . $regionId);
      }
      $this->region = $region;
    }
    catch(\UnexpectedValueException $e){
      Logger::getRootLogger()->warn($e->getMessage());
      $this->region = App::getRegion()->getById(self::DEFAULT_REGION_ID);
    }

    $this->cart = new Cart(new SessionCartContainer(), new V2CartPriceContainer());
  }
}
