<?php

class BaseCriteria
{
  protected
    $user = null,
    $region = null,
    $pager = null
  ;

  public function __construct()
  {
    $this->user = sfContext::getInstance()->getUser()->getGuardUser();
    $this->region = sfContext::getInstance()->getUser()->getRegion('region');
  }

  public function setUser(GuardUser $user)
  {
    $this->user = $user;

    return $this;
  }

  public function getUser()
  {
    return $this->user;
  }

  public function setRegion(Region $region)
  {
    $this->region = $region;

    return $this;
  }

  public function getRegion()
  {
    return $this->region;
  }

  public function setPager($pager) // PagerInterface $pager
  {
    $this->pager = $pager;

    return $this;
  }

  public function getPager()
  {
    return $this->pager;
  }
}