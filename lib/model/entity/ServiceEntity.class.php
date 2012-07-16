<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 05.06.12
 * Time: 12:33
 * To change this template use File | Settings | File Templates.
 */
class ServiceEntity
{
  /**
   * @var int
   */
  private $id;

  /**
   * @var string
   */
  private $token;

  /**
   * @var string
   */
  private $name;

  /**
   * @var float
   */
  private $price;

  /**
   * @var string
   */
  private $mediaImage;

  public function __construct(array $data = array()){
    if(array_key_exists('id', $data))          $this->id         = (int)$data['id'];
    if(array_key_exists('token', $data))       $this->token      = (string)$data['token'];
    if(array_key_exists('name', $data))        $this->name       = (string)$data['name'];
    if(array_key_exists('price', $data))       $this->price      = (float)$data['price'];
    if(array_key_exists('media_image', $data)) $this->mediaImage = (string)$data['media_image'];

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
   * @param float $price
   */
  public function setPrice($price)
  {
    $this->price = $price;
  }

  /**
   * @return float
   */
  public function getPrice()
  {
    return $this->price;
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
   * @return string
   */
  public function getMediaImage()
  {
    return $this->mediaImage;
  }

}
