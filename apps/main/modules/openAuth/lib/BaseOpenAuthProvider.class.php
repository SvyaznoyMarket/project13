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

  public function getConfig()
  {
    return $this->configHolder->getAll();
  }
}