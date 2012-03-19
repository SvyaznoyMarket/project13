<?php

class RegionCriteria extends BaseCriteria
{
  protected
    $token = null
  ;

  public function setToken($token)
  {
    $this->token = $token;

    return $this;
  }

  public function getToken()
  {
    return $this->token;
  }
}