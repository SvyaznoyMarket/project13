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
      throw new InvalidArgumentException('region id is not valid: '.$id);
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
        throw new UnexpectedValueException('not found region for id: ' . $regionId);
      }
      $this->region = $region;
    }
    catch(UnexpectedValueException $e){
      Logger::getRootLogger()->warn($e->getMessage());
      $this->region = App::getRegion()->getById(self::DEFAULT_REGION_ID);
    }
  }
}