<?php

abstract class BaseOpenAuthProvider
{
  protected $configHolder = null;

  public function __construct(array $config)
  {
    $this->configHolder = new sfParameterHolder();
    $this->configHolder->add($config);
  }

  abstract public function getData();

  abstract public function getProfile(sfWebRequest $request);

  public function getConfig($name = null)
  {
    return null == $name ? $this->configHolder->getAll() : $this->configHolder->get($name);
  }
}