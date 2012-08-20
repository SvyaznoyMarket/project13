<?php

class UserEntity implements ArrayAccess
{
  const GENDER_MALE = 2;
  const GENDER_FEMALE = 1;

  /* @var string */
  private $token;

  /* @var string */
  private $id;

  /* @var string */
  private $email;

  /* @var string */
  private $phonenumber;

  /* @var string */
  private $homePhonenumber;

  /* @var string */
  private $firstName;

  /* @var string */
  private $lastName;

  /* @var string */
  private $middleName;

  /* @var string */
  private $lastLogin;

  /* @var string */
  private $lastIp;

  /* @var RegionEntity */
  private $region;

  /* @var integer */
  private $type;

  /* @var integer */
  private $gender;

  /* @var string */
  private $birthday;

  /* @var string */
  private $photo;

  /* @var string */
  private $skype;

  /* @var string */
  private $occupation;

  /* @var string */
  private $address;

  /* @var string */
  private $zipCode;

  /* @var bool */
  private $subscribed;


  public function __construct(array $data = array()){
    if(array_key_exists('token', $data))         $this->token       = (string)$data['token'];
    if(array_key_exists('id', $data))            $this->id          = (int)$data['id'];
    if(array_key_exists('email', $data))         $this->email       = (string)$data['email'];
    if(array_key_exists('mobile', $data))        $this->phonenumber = (string)$data['mobile'];
    if(array_key_exists('phone', $data))         $this->homePhonenumber = (string)$data['phone'];
    if(array_key_exists('first_name', $data))    $this->firstName   = (string)$data['first_name'];
    if(array_key_exists('last_name', $data))     $this->lastName    = (string)$data['last_name'];
    if(array_key_exists('middle_name', $data))   $this->middleName  = (string)$data['middle_name'];
    if(array_key_exists('last_login', $data))    $this->lastLogin   = (string)$data['last_login'];
    if(array_key_exists('ip', $data))            $this->lastIp      = (string)$data['ip'];
    if(array_key_exists('geo', $data))           $this->region      = new Region($data['geo']);
    if(array_key_exists('type_id', $data))       $this->type        = (int)$data['type_id'];
    if(array_key_exists('sex', $data))           $this->gender      = (int)$data['sex'];
    if(array_key_exists('birthday', $data))      $this->birthday    = (string)$data['birthday'];
    if(array_key_exists('skype', $data))         $this->skype       = (string)$data['skype'];
    if(array_key_exists('occupation', $data))    $this->occupation  = (string)$data['occupation'];
    if(array_key_exists('address', $data))       $this->address     = (string)$data['address'];
    if(array_key_exists('zip_code', $data))      $this->zipCode     = (string)$data['zip_code'];
    if(array_key_exists('is_subscribe', $data))  $this->subscribed  = (string)$data['is_subscribe'];
  }

  public function __get($key){

    return call_user_func(array($this, 'get'.sfInflector::camelize($key)));
  }

  public function __set($key, $value){

    call_user_func_array(array($this, 'set'.sfInflector::camelize($key)), array($value));
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
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param string $birthday
   */
  public function setBirthday($birthday)
  {
    $this->birthday = $birthday;
  }

  /**
   * @return string
   */
  public function getBirthday()
  {
    return $this->birthday;
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
   * @param int $gender
   */
  public function setGender($gender)
  {
    $this->gender = $gender;
  }

  /**
   * @return int
   */
  public function getGender()
  {
    return $this->gender;
  }

  /**
   * @param string $lastIp
   */
  public function setLastIp($lastIp)
  {
    $this->lastIp = $lastIp;
  }

  /**
   * @return string
   */
  public function getLastIp()
  {
    return $this->lastIp;
  }

  /**
   * @param string $lastLogin
   */
  public function setLastLogin($lastLogin)
  {
    $this->lastLogin = $lastLogin;
  }

  /**
   * @return string
   */
  public function getLastLogin()
  {
    return $this->lastLogin;
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

  /**
   * @param string $occupation
   */
  public function setOccupation($occupation)
  {
    $this->occupation = $occupation;
  }

  /**
   * @return string
   */
  public function getOccupation()
  {
    return $this->occupation;
  }

  /**
   * @param string $phonenumber
   */
  public function setPhonenumber($phonenumber)
  {
    $this->phonenumber = $phonenumber;
  }

  /**
   * @return string
   */
  public function getPhonenumber()
  {
    return $this->phonenumber;
  }

  /**
   * @param string $homePhonenumber
   */
  public function setHomePhonenumber($homePhonenumber)
  {
    $this->homePhonenumber = $homePhonenumber;
  }

  /**
   * @return string
   */
  public function getHomePhonenumber()
  {
    return $this->homePhonenumber;
  }

  /**
   * @param string $photo
   */
  public function setPhoto($photo)
  {
    $this->photo = $photo;
  }

  /**
   * @return string
   */
  public function getPhoto()
  {
    return $this->photo;
  }

  /**
   * @param \RegionEntity $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }

  /**
   * @return \RegionEntity
   */
  public function getRegion()
  {
    return $this->region;
  }

  /**
   * @param string $skype
   */
  public function setSkype($skype)
  {
    $this->skype = $skype;
  }

  /**
   * @return string
   */
  public function getSkype()
  {
    return $this->skype;
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
   * @param string $zipCode
   */
  public function setZipCode($zipCode)
  {
    $this->zipCode = $zipCode;
  }

  /**
   * @return string
   */
  public function getZipCode()
  {
    return $this->zipCode;
  }

  /**
   * @param boolean $isSubscribed
   */
  public function setSubscribed($subscribed)
  {
    $this->subscribed = $subscribed;
  }

  /**
   * @return boolean
   */
  public function getSubscribed()
  {
    return $this->subscribed;
  }

  /**
   * @param int $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  /**
   * @return int
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Returns the string representation of the object
   *
   * @return string
   */
  public function __toString()
  {
    return (string)$this->getName();
  }

  /**
   * Returns the first and last name of the user concatenated together
   *
   * @return string $name
   */
  public function getName()
  {
    return implode(' ', array($this->getFirstName(), $this->getLastName()));
  }

  /**
   * Returns full name
   *
   * @return string
   */
  public function getFullName()
  {
    return implode(' ', array($this->getLastName(), $this->getFirstName(), $this->getMiddleName()));
  }

  public function getPermissionNames()
  {
    return array();
  }

  /**
   * Returns true if the request parameter exists (implements the ArrayAccess interface).
   *
   * @param  string $name The name of the request parameter
   *
   * @return Boolean true if the request parameter exists, false otherwise
   */
  public function offsetExists($name)
  {
    return method_exists($this, 'get'.sfInflector::camelize($name));
  }

  /**
   * Returns the request parameter associated with the name (implements the ArrayAccess interface).
   *
   * @param  string $name  The offset of the value to get
   *
   * @return mixed The request parameter if exists, null otherwise
   */
  public function offsetGet($name)
  {
    return call_user_func(array($this, 'get'.sfInflector::camelize($name)));
  }

  /**
   * Sets the request parameter associated with the offset (implements the ArrayAccess interface).
   *
   * @param string $offset The parameter name
   * @param string $value The parameter value
   */
  public function offsetSet($offset, $value)
  {
    call_user_func(array($this, 'set'.sfInflector::camelize($offset)), array($value));
  }

  /**
   * Removes a request parameter.
   *
   * @param string $offset The parameter name
   */
  public function offsetUnset($offset)
  {
    call_user_func(array($this, 'set'.sfInflector::camelize($offset)), array(null));
  }
}