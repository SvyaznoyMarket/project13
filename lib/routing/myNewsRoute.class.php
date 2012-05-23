<?php

class myNewsRoute extends myDoctrineRoute
{

  protected $newsCategory = null;

  public function matchesUrl($url, $context = array())
  {
    if (false === $parameters = parent::matchesUrl($url, $context))
    {
      return false;
    }

    $this->newsCategory = NewsCategoryTable::getInstance()->getForRoute($parameters);

    return $parameters;
  }

  public function generate($params, $context = array(), $absolute = false, array $allow = array('/'))
  {
    if (isset($params['newsCategory']) && ($params['newsCategory'] instanceof NewsCategory))
    {
      $params = array_merge($params, $params['newsCategory']->toParams());
    }

    return parent::generate($params, $context, $absolute);
  }

  public function getNewsCategoryObject()
  {
    return $this->newsCategory;
  }

}
