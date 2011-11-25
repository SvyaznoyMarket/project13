<?php

class myShopRoute extends myDoctrineRoute
{

  protected $region = null;

  public function matchesUrl($url, $context = array())
  {
    if (false === $parameters = parent::matchesUrl($url, $context))
    {
      return false;
    }

    $this->region = RegionTable::getInstance()->getForRoute($parameters);

    return $parameters;
  }

  public function generate($params, $context = array(), $absolute = false)
  {
    if (isset($params['sf_subject']) && ($params['sf_subject'] instanceof Shop))
    {
      $params = array_merge($params, $params['sf_subject']->Region->toParams());
    }

    return parent::generate($params, $context, $absolute);
  }

  public function getCreatorObject()
  {
    return $this->creator;
  }

}