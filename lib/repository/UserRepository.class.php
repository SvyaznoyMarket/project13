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
}