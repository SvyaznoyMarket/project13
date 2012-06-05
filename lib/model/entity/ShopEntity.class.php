<?php

class ShopEntity
{
  /* @var string */
  private $token;

  /* @var string */
  private $name;

  /* @var string */
  private $regime;

  /* @var string */
  private $address;

  /* @var double */
  private $latitude;

  /* @var double */
  private $longitude;


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
   * @param \double $latitude
   */
  public function setLatitude($latitude)
  {
    $this->latitude = $latitude;
  }

  /**
   * @return \double
   */
  public function getLatitude()
  {
    return $this->latitude;
  }

  /**
   * @param \double $longitude
   */
  public function setLongitude($longitude)
  {
    $this->longitude = $longitude;
  }

  /**
   * @return \double
   */
  public function getLongitude()
  {
    return $this->longitude;
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
   * @param string $regime
   */
  public function setRegime($regime)
  {
    $this->regime = $regime;
  }

  /**
   * @return string
   */
  public function getRegime()
  {
    return $this->regime;
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
}