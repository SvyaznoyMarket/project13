<?php

abstract class BaseOpenAuthProvider
{
  protected $configHolder = null;

  public function __construct(array $config)
  {
    $this->configHolder = new sfParameterHolder();
    $this->configHolder->add($config);
  }

  public function getConfig($name = null)
  {
    return null == $name ? $this->configHolder->getAll() : $this->configHolder->get($name);
  }

  public function generateUrl($route, $params = array(), $absolute = false)
  {
    return sfContext::getInstance()->getRouting()->generate($route, $params, $absolute);
  }

  //abstract public function getSigninUrl();
  //abstract public function getProfile(sfWebRequest $request, myUser $user);
}