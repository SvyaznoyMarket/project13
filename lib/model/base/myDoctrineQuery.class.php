<?php

class myDoctrineQuery extends Doctrine_Query
{
  public function useResultCache($driver = true, $timeToLive = null, $resultCacheHash = null)
  {
    if (false === sfConfig::get('app_doctrine_result_cache'))
    {
      $driver = null;
    }
    
    return parent::useResultCache($driver, $timeToLive, $resultCacheHash);
  }
}