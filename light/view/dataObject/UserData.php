<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 28.06.12
 * Time: 21:47
 * To change this template use File | Settings | File Templates.
 */
class UserData
{

  /** @var int */
  private $id;

  /** @var string */
  private $firstName;

  /** @var string */
  private $lastName;

  /** @var string */
  private $middleName;

  /** @var string */
  private $email;


  public function __construct($data=array()){
    if(array_key_exists('id', $data)){ $this->setId((int) $data['id']); }
    if(array_key_exists('first_name', $data)){ $this->setFirstName((string) $data['first_name']); }
    if(array_key_exists('last_name', $data)){ $this->setLastName((int) $data['last_name']); }
    if(array_key_exists('middle_name', $data)){ $this->setMiddleName((int) $data['middle_name']); }
    if(array_key_exists('email', $data)){ $this->setEmail((int) $data['email']); }
  }

  /**
   * @return string
   */
  public function getFullName(){
    $name = '';
    if(strlen($this->getFirstName()) > 0){
      $name .= ' ' . $this->getFirstName();
    }
    if(strlen($this->getMiddleName()) > 0){
      $name .= ' ' . $this->getMiddleName();
    }
    if(strlen($this->getLastName()) > 0){
      $name .= ' ' . $this->getLastName();
    }

    return $name;
  }

  /**
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @param string $firstName
   */
  public function setFirstName($firstName)
  {
    $this->firstName = $firstName;
  }

  /**
   * @return string
   */
  public function getFirstName()
  {
    return $this->firstName;
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
   * @param string $lastName
   */
  public function setLastName($lastName)
  {
    $this->lastName = $lastName;
  }

  /**
   * @return string
   */
  public function getLastName()
  {
    return $this->lastName;
  }

  /**
   * @param string $middleName
   */
  public function setMiddleName($middleName)
  {
    $this->middleName = $middleName;
  }

  /**
   * @return string
   */
  public function getMiddleName()
  {
    return $this->middleName;
  }

}
