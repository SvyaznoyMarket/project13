<?php

class DeliveryTypeEntity
{
  /* @var integer */
  private $id;

  /* @var string */
  private $token;

  /* @var string */
  private $name;

  /* @var string */
  private $description;

  public function __construct($data){

    $this->id =          array_key_exists('id', $data) ? (int)$data['id'] : null;
    $this->token =       array_key_exists('token', $data) ? (string)$data['token'] : '';
    $this->name =        array_key_exists('name', $data) ? (string)$data['name'] : '';
    $this->description = array_key_exists('description', $data) ? (string)$data['description'] : '';

  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
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