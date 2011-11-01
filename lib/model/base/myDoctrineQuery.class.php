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

  public function hasAliasDeclaration($componentAlias)
  {
    // for fix bug?
    $this->getSqlQuery();

    return parent::hasAliasDeclaration($componentAlias);
  }

  public function count($params = array())
  {
    $q = $this->getCountSqlQuery();
    $params = $this->getCountQueryParams($params);
    $params = $this->_conn->convertBooleans($params);

    if ($this->_resultCache)
    {
      $conn = $this->getConnection();
      $cacheDriver = $this->getResultCacheDriver();
      $hash = $this->getResultCacheHash($params).'_count';
      $cached = ($this->_expireResultCache) ? false : $cacheDriver->fetch($hash);

      if ($cached === false)
      {
        // cache miss
        $results = $this->getConnection()->fetchAll($q, $params);
        $cacheDriver->save($hash, serialize($results), $this->getResultCacheLifeSpan());
      }
      else
      {
        // @green fix
        $results = is_string($cached) ? unserialize($cached) : $cached;
      }
    }
    else
    {
      $results = $this->getConnection()->fetchAll($q, $params);
    }

    if (count($results) > 1)
    {
      $count = count($results);
    }
    else
    {
      if (isset($results[0]))
      {
        $results[0] = array_change_key_case($results[0], CASE_LOWER);
        $count = $results[0]['num_results'];
      }
      else
      {
        $count = 0;
      }
    }

    return (int) $count;
  }

}