<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 13.04.12
 * Time: 14:34
 * To change this template use File | Settings | File Templates.
 */
class ShopData
{

  /**
   * @var integer
   */
  private $id = Null;

  /**
   * @var string
   */
  private $regtime = Null;

  /**
   * @var string
   */
  private $address = Null;

  /**
   * @var string
   */
  private $latitude = Null;

  /**
   * @var string
   */
  private $longitude = Null;

  /**
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }

  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
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
   * @param string $latitude
   */
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;
  }

  /**
   * @return string
   */
  public function getLatitude()
  {
    return $this->latitude;
  }

  /**
   * @param string $longitude
   */
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;
  }

  /**
   * @return string
   */
  public function getLongitude()
  {
    return $this->longitude;
  }

  /**
   * @param string $regtime
   */
  public function setRegtime($regtime)
  {
    $this->regtime = $regtime;
  }

  /**
   * @return string
   */
  public function getRegtime()
  {
    return $this->regtime;
  }

  public function toArray(){
    return array(
      'id'        => $this->getId(),
      'regtime'   => $this->getRegtime(),
      'address'   => $this->getAddress(),
      'latitude'  => $this->getLatitude(),
      'longitude' => $this->getLongitude()
    );
  }
}
