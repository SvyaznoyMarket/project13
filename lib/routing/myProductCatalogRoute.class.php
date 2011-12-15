<?php

class myProductCatalogRoute extends myDoctrineRoute
{

  protected $creator = null;

  public function matchesUrl($url, $context = array())
  {
    if (false === $parameters = parent::matchesUrl($url, $context))
    {
      return false;
    }

    $this->creator = CreatorTable::getInstance()->getForRoute($parameters);

    return $parameters;
  }

  public function generate($params, $context = array(), $absolute = false, array $allow = array('/'))
  {

    if (isset($params['creator']) && ($params['creator'] instanceof Creator))
    {
      $params = array_merge($params, $params['creator']->toParams());
    }

    return parent::generate($params, $context, $absolute);
  }

  public function getCreatorObject()
  {
    return $this->creator;
  }

}