<?php

class UserRepository
{
  /* @var CoreClient */
  private $coreClient = null;

  public function __construct()
  {
    $this->coreClient = CoreClient::getInstance();
  }

  public function getByToken($token) {
    $return = null;

    $result = $this->coreClient->query('user/get', array('token' => $token));

    if ($result) {
      $return = new UserEntity($result);
    }

    return $return;
  }

  public function update(UserEntity $user) {
    $data = array(
      'first_name'   => $user->getFirstName(),
      'last_name'    => $user->getLastName(),
      'middle_name'  => $user->getMiddleName(),
      'sex'          => $user->getGender(),
      'birthday'     => $user->getBirthday(),
      'occupation'   => $user->getOccupation(),
      'email'        => $user->getEmail(),
      'mobile'       => $user->getPhonenumber(),
      'skype'        => $user->getSkype(),
      'address'      => $user->getAddress(),
      'zip_code'     => $user->getZipCode(),
      'is_subscribe' => $user->getSubscribed(),
      'last_login'   => $user->getLastLogin(),
      'ip'           => $user->getLastIp(),
    );

    return $this->coreClient->query('user/update', array('token' => $user->getToken()), $data);
  }
}