<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 12.05.12
 * Time: 16:32
 * To change this template use File | Settings | File Templates.
 */
class RegionData
{

  /**
   * @var int
   */
  private $id;

  /**
   * @var int | Null
   */
  private $geoIpCode = Null;

  /**
   * @var string
   */
  private $token;

  /**
   * @var string
   */
  private $name;

  /**
   * @var bool
   */
  private $isMain;

  /** @var bool */
  private $hasTransportCompany;


  /**
   * @param int $geoIpCode
   */
  public function setGeoIpCode($geoIpCode)
  {
    $this->geoIpCode = $geoIpCode;
  }

  /**
   * @return int | Null
   */
  public function getGeoIpCode()
  {
    return $this->geoIpCode;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param bool $isMain
   */
  public function setIsMain($isMain)
  {
    $this->isMain = $isMain;
  }

  /**
   * @return bool
   */
  public function getIsMain()
  {
    return $this->isMain;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }

  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * @param boolean $hasTransportCompany
   */
  public function setHasTransportCompany($hasTransportCompany) {
    $this->hasTransportCompany = $hasTransportCompany;
  }

  /**
   * @return boolean
   */
  public function getHasTransportCompany() {
    return $this->hasTransportCompany;
  }


}
