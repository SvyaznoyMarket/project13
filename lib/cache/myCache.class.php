<?php

class myCache
{
  protected static
    $instance = null
  ;

  static public function getInstance()
  {
    if (null == self::$instance)
    {
      self::$instance = new myRedisCache(sfConfig::get('app_cache_config'));
    }

    return self::$instance;
  }
}