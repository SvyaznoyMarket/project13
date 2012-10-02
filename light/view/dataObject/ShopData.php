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

  /** @var integer */
  private $id = Null;

  /** @var string */
  private $name = Null;

  /** @var string */
  private $regtime = Null;

  /** @var string */
  private $address = Null;

  /** @var string */
  private $latitude = Null;

  /** @var string */
  private $longitude = Null;

  /** @var int */
  private $regionId = Null;

  /**
   * @param array $data
   */
  public function __construct($data = array()){
    if(array_key_exists('id', $data))        $this->setId((int)$data['id']);
    if(array_key_exists('name', $data))      $this->setName((string)$data['name']);
    if(array_key_exists('geo', $data) && is_array($data['geo']) && array_key_exists('id', $data['geo'])) $this->setRegionId((int)$data['geo']['id']);
    if(array_key_exists('regtime', $data))   $this->setRegtime((string)$data['regtime']);
    if(array_key_exists('working_time', $data))   $this->setRegtime((string)$data['working_time']);
    if(array_key_exists('address', $data))   $this->setAddress((string)$data['address']);
    if(array_key_exists('latitude', $data))  $this->setLatitude((string)$data['latitude']);
    if(array_key_exists('coord_lat', $data))  $this->setLatitude((string)$data['coord_lat']);
    if(array_key_exists('longitude', $data)) $this->setLongitude((string)$data['longitude']);
    if(array_key_exists('coord_long', $data)) $this->setLongitude((string)$data['coord_long']);
  }

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

  public function setRegionId($regionId)
  {
    $this->regionId = $regionId;
  }

  public function getRegionId()
  {
    return $this->regionId;
  }
}
