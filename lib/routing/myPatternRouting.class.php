<?php

class myPatternRouting extends sfPatternRouting
{
  public function generate($params, $context = array(), $absolute = false, array $allow = array('/'))
  {
    $url = parent::generate($params, $context, $absolute);

    if (in_array('/', $allow))
    {
      $url = str_replace('%2F','/', $url);
    }

    return $url;
  }
}